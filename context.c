/* context extension for PHP */

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include "terminate.h"
#include "ctx.h"
#include "swap_context.h"

#include "php.h"
#include "ext/standard/info.h"
#include "php_context.h"
#include "context_arginfo.h"
#include "zend_closures.h"
#include "zend_exceptions.h"
#include "zend_compile.h"

/* For compatibility with older PHP versions */
#ifndef ZEND_PARSE_PARAMETERS_NONE
#define ZEND_PARSE_PARAMETERS_NONE() \
	ZEND_PARSE_PARAMETERS_START(0, 0) \
	ZEND_PARSE_PARAMETERS_END()
#endif

extern PHPAPI zend_class_entry *spl_ce_RuntimeException;
static zend_object_handlers context_object_handlers;

typedef struct {
	context * ctx;
	zval *bridge;
	zend_execute_data * root;
	zval closure;
    zend_object std;
} context_zend_object;



static context_zend_object * current	= NULL;
static context * primary 				= NULL;
static void (*orig_interrupt_function)(zend_execute_data *execute_data);

#define CONTEXT_INTERRUPT_IN 	1;
#define CONTEXT_INTERRUPT_OUT 	2;

static unsigned int context_interrupt = 0;
static unsigned int context_interrupt_type = 0;

#define REGISTER_IN_INTERRUPT 	EG(vm_interrupt) = 1;\
context_interrupt = 1; \
context_interrupt_type = CONTEXT_INTERRUPT_IN;

#define REGISTER_OUT_INTERRUPT 	EG(vm_interrupt) = 1;\
context_interrupt = 1; \
context_interrupt_type = CONTEXT_INTERRUPT_OUT;

#define FLUSH_INTERRUPT context_interrupt = 0;\
context_interrupt_type = 0;

#define NEED_INTERRUPT (context_interrupt == 1)
#define IS_IN_INTERRUPT (context_interrupt_type == 1)

zend_function *f;

static void context_cleanup_unfinished_execution(zend_execute_data *execute_data) /* {{{ */
{
	if (execute_data->opline != execute_data->func->op_array.opcodes) {
		/* -1 required because we want the last run opcode, not the next to-be-run one. */
		uint32_t op_num = execute_data->opline - execute_data->func->op_array.opcodes - 1;

		zend_cleanup_unfinished_execution(execute_data, op_num, 0);
	}
}

static void free_context_zend_object_internal_data(context_zend_object *obj)
{
	if(obj->ctx == NULL){
		return;
	}

	zend_execute_data * call = obj->ctx->current_execute_data;
	while(call){
		if (ZEND_CALL_INFO(call) & ZEND_CALL_HAS_SYMBOL_TABLE) {
			zend_clean_and_cache_symbol_table(call->symbol_table);
        }

		zend_free_compiled_variables(call);

		if (ZEND_CALL_INFO(call) & ZEND_CALL_HAS_EXTRA_NAMED_PARAMS) {
        	zend_free_extra_named_params(call->extra_named_params);
        }

        if (ZEND_CALL_INFO(call) & ZEND_CALL_RELEASE_THIS) {
			OBJ_RELEASE(Z_OBJ(call->This));
		}

		if (UNEXPECTED(CG(unclean_shutdown))) {
			break;
		}

		zend_vm_stack_free_extra_args(call);

		context_cleanup_unfinished_execution(call);

		if (ZEND_CALL_INFO(call) & ZEND_CALL_CLOSURE) {
			OBJ_RELEASE(ZEND_CLOSURE_OBJECT(call->func));
        }

		// clear frame
		call = call->prev_execute_data;
	}
	free_context(obj->ctx);

	zval_ptr_dtor(&obj->closure);
    obj->ctx = NULL;
}

int trap_op_handler()
{
	save_context(current->ctx);
	set_context(primary);

	if (UNEXPECTED(EG(exception))) {
		zend_rethrow_exception(EG(current_execute_data));
    }

    free_context_zend_object_internal_data(current);
    current = NULL;

	return ZEND_USER_OPCODE_ENTER;
}


static zend_always_inline context_zend_object *context_zend_object_from_obj(zend_object *obj) {
	return (context_zend_object *)((char *)(obj) - XtOffsetOf(context_zend_object, std));
}

static void context_interrupt_function(zend_execute_data *execute_data)
{
	if(NEED_INTERRUPT){
		if(IS_IN_INTERRUPT){
			save_context(primary);
			set_context(current->ctx);
		}else{
			save_context(current->ctx);
			set_context(primary);
			current = NULL;
		}
		FLUSH_INTERRUPT
	}

	if (orig_interrupt_function) {
		orig_interrupt_function(execute_data);
    }
}

ZEND_METHOD(Interposition_Context, __construct)
{
	zval *closure = NULL;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_OBJECT_OF_CLASS(closure, zend_ce_closure)
	ZEND_PARSE_PARAMETERS_END();

	zend_function * func;
	void *object_or_called_scope;
	zend_class_entry *called_scope;
	zend_object *object;

	if(Z_OBJ_P(closure)->handlers->get_closure(Z_OBJ_P(closure), &called_scope, &func, &object, 0) != SUCCESS){
		zend_argument_error(NULL, 1, "Could noy get closure.");
        RETURN_THROWS();
	}

	if(func->common.num_args > 0){
		zend_argument_error(NULL, 1, "must have no arguments.");
		RETURN_THROWS();
	}

	context_zend_object * obj = context_zend_object_from_obj(Z_OBJ_P(getThis()));

	ZVAL_COPY(&obj->closure, closure);

	context * ctx = create_context();

	push_function(ctx, f, ZEND_CALL_TOP_FUNCTION, NULL);

    push_function(ctx, func, ZEND_CALL_NESTED_FUNCTION | ZEND_CALL_CLOSURE | ZEND_CALL_DYNAMIC, object);
	obj->root = ctx->current_execute_data;

	GC_ADDREF(Z_COUNTED_P(closure));

    obj->ctx = ctx;
}

ZEND_METHOD(Interposition_Context, resume)
{
	if(current != NULL){
		zend_throw_exception_ex(spl_ce_RuntimeException, 0, "Could't resume context in another context!");
        RETURN_THROWS();
	}

	context_zend_object * obj = context_zend_object_from_obj(Z_OBJ_P(getThis()));

	zval *send = NULL;

	ZEND_PARSE_PARAMETERS_START(0, 1)
		Z_PARAM_OPTIONAL
    	Z_PARAM_ZVAL(send)
    ZEND_PARSE_PARAMETERS_END();

	if(obj->ctx == NULL){
		zend_throw_exception_ex(spl_ce_RuntimeException, 0, "Ctx completed or throwed!");
    	RETURN_THROWS();
	}

	if(send != NULL && obj->bridge != NULL){
		ZVAL_COPY(obj->bridge, send);
	}


    obj->bridge = USED_RET() ? return_value : NULL;
	obj->root->return_value = USED_RET() ? return_value : NULL;

	current = obj;
	REGISTER_IN_INTERRUPT


}

ZEND_METHOD(Interposition_Context, finished)
{
	context_zend_object * obj = context_zend_object_from_obj(Z_OBJ_P(getThis()));

	RETURN_BOOL(obj->ctx == NULL);
}

ZEND_METHOD(Interposition_Context, suspend)
{
	if(current == NULL){
		zend_throw_exception_ex(spl_ce_RuntimeException, 0, "Could't suspend outside context!");
        RETURN_THROWS();
	}


	context_zend_object * obj = current;

	zval *send = NULL;

	ZEND_PARSE_PARAMETERS_START(0, 1)
		Z_PARAM_OPTIONAL
		Z_PARAM_ZVAL(send)
	ZEND_PARSE_PARAMETERS_END();

	 if(send != NULL && obj->bridge != NULL){
       	ZVAL_COPY(obj->bridge, send);
     }

    obj->bridge = USED_RET() ? return_value : NULL;

	REGISTER_OUT_INTERRUPT
}

static zend_object * create_context_zend_object(zend_class_entry * class_entry)
{
	context_zend_object *internal = zend_object_alloc(sizeof(context_zend_object), class_entry);

	internal->bridge 	 			 = NULL;
	internal->root 	 = NULL;

    zend_object_std_init(&internal->std, class_entry);
    object_properties_init(&internal->std, class_entry);

    internal->std.handlers = &context_object_handlers;

	return &internal->std;
}

static void free_context_zend_object(zend_object * object)
{
	context_zend_object *internal = context_zend_object_from_obj(object);

	free_context_zend_object_internal_data(internal);
	zend_object_std_dtor(&internal->std);
}

PHP_MSHUTDOWN_FUNCTION(context)
{
	destroy_terminate_func(f);
	return SUCCESS;
}

PHP_MINIT_FUNCTION(context)
{
	memcpy(&context_object_handlers, &std_object_handlers, sizeof(zend_object_handlers));

	context_object_handlers.free_obj = free_context_zend_object;
	context_object_handlers.offset = XtOffsetOf(context_zend_object, std);


	// register context class
	zend_class_entry origin_class_entry;

	INIT_CLASS_ENTRY(origin_class_entry, "Interposition\\Context", class_Interposition_Context_methods);

	zend_class_entry *registered_class_entry = zend_register_internal_class (&origin_class_entry);

	// set custom object constructor
	registered_class_entry->create_object = create_context_zend_object;

	f = register_terminate_func(trap_op_handler);
	if(f == NULL){
		return FAILURE;
	}

	orig_interrupt_function = zend_interrupt_function;
	zend_interrupt_function = context_interrupt_function;

	return SUCCESS;
}

PHP_RINIT_FUNCTION(context)
{
	#if defined(ZTS) && defined(COMPILE_DL_CONTEXT)
		ZEND_TSRMLS_CACHE_UPDATE();
	#endif

	primary = (context *)emalloc(sizeof(context));

	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(context)
{
	efree(primary);
	return SUCCESS;
}

PHP_MINFO_FUNCTION(context)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "context support", "enabled");
	php_info_print_table_end();
}

zend_module_entry context_module_entry = {
	STANDARD_MODULE_HEADER,
	"context",												/* Extension name */
	NULL,													/* zend_function_entry */
	PHP_MINIT(context),										/* PHP_MINIT - Module initialization */
	PHP_MSHUTDOWN(context),													/* PHP_MSHUTDOWN - Module shutdown */
	PHP_RINIT(context),										/* PHP_RINIT - Request initialization */
	PHP_RSHUTDOWN(context),													/* PHP_RSHUTDOWN - Request shutdown */
	PHP_MINFO(context),										/* PHP_MINFO - Module info */
	PHP_CONTEXT_VERSION,									/* Version */
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_CONTEXT
# ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
# endif
ZEND_GET_MODULE(context)
#endif
