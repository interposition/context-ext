#include "ctx.h"
#include "zend_compile.h"

static zend_vm_stack vm_stack_new_page(size_t size)
{
	zend_vm_stack page = (zend_vm_stack)emalloc(size);
	page->top = ZEND_VM_STACK_ELEMENTS(page);
	page->end = (zval*)((char*)page + size);
	page->prev = NULL;

	return page;
}

static zend_always_inline void vm_stack_free(zend_vm_stack stack)
{
	while (stack != NULL) {
		zend_vm_stack p = stack->prev;
		efree(stack);
		stack = p;
	}
}

context *create_context()
{
	context * ctx = emalloc(sizeof(context));
	memset(ctx, 0, sizeof(context));

	ctx->vm_stack_page_size = 1024 * 8;
    ctx->vm_stack			= vm_stack_new_page(ctx->vm_stack_page_size);
	ctx->vm_stack_top 		= ctx->vm_stack->top;
	ctx->vm_stack_end 		= ctx->vm_stack->end;

	return ctx;
}

void free_context(context *ctx)
{
	vm_stack_free(ctx->vm_stack);
	efree(ctx);
}

void push_function(context *ctx, zend_function * func, uint32_t call_info, 	void *object_or_called_scope)
{
	zend_execute_data * call = (zend_execute_data *)ctx->vm_stack_top;
	memset(call, 0, sizeof(zend_execute_data));

	// calc stack size (without arguments)
    uint32_t used_stack = ZEND_CALL_FRAME_SLOT + (func->op_array.last_var  + func->op_array.T) * sizeof(zval);

	ctx->vm_stack_top 		= (zval*)call + used_stack;

	call->opline 			= func->op_array.opcodes;
	call->call 		      	= NULL;
	call->return_value 	  	= NULL;
	call->prev_execute_data = ctx->current_execute_data;
	call->func				= func;
	ZEND_CALL_INFO(call) 	= call_info;


	if(object_or_called_scope){
		 Z_PTR(call->This)		= object_or_called_scope;
		 ZEND_CALL_INFO(call) 	=  ZEND_CALL_INFO(call) | ZEND_CALL_HAS_THIS;
	}

	ZEND_CALL_NUM_ARGS(call) = 0;

	// zend_init_cvs
	if (EXPECTED(0 < func->op_array.last_var)) {
    		uint32_t count = func->op_array.last_var - 0;
    		zval *var =ZEND_CALL_VAR_NUM(call, 0);

    		do {
    			ZVAL_UNDEF(var);
    			var++;
    		} while (--count);
	}

	if(!(call_info & ZEND_CALL_TOP_FUNCTION)){
		if (!ZEND_MAP_PTR(func->op_array.run_time_cache)) {
        		void *ptr;

            	ptr = emalloc(func->op_array.cache_size + sizeof(void*));

        		ZEND_MAP_PTR_INIT(func->op_array.run_time_cache, ptr);
            	ptr = (char*)ptr + sizeof(void*);
        		ZEND_MAP_PTR_SET(func->op_array.run_time_cache, ptr);
        		memset(ptr, 0, func->op_array.cache_size);
            }

        	call->run_time_cache 	= RUN_TIME_CACHE(&func->op_array);
	}



	ctx->current_execute_data = call;
}


