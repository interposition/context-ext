#include "init_op_array.h"
#include "zend_extensions.h"

static void zend_extension_op_array_ctor_handler(zend_extension *extension, zend_op_array *op_array)
{
	if (extension->op_array_ctor) {
		extension->op_array_ctor(op_array);
	}
}

// see init_op_array zend_opcode.c
void _init_op_array(zend_op_array *op_array, zend_uchar type, int initial_ops_size)
{
	op_array->type = type;
	op_array->arg_flags[0] = 0;
	op_array->arg_flags[1] = 0;
	op_array->arg_flags[2] = 0;

	op_array->refcount = (uint32_t *) pemalloc(sizeof(uint32_t), 1);
	*op_array->refcount = 1;
	op_array->last = 0;
	op_array->opcodes = pemalloc(initial_ops_size * sizeof(zend_op), 1);

	op_array->last_var = 0;
	op_array->vars = NULL;

	op_array->T = 0;

	op_array->function_name = NULL;
	op_array->filename = zend_string_init("", 0, 0);
	op_array->doc_comment = NULL;
	op_array->attributes = NULL;

	op_array->arg_info = NULL;
	op_array->num_args = 0;
	op_array->required_num_args = 0;

	op_array->scope = NULL;
	op_array->prototype = NULL;

	op_array->live_range = NULL;
	op_array->try_catch_array = NULL;
	op_array->last_live_range = 0;

	op_array->static_variables = NULL;
	ZEND_MAP_PTR_INIT(op_array->static_variables_ptr, &op_array->static_variables);
	op_array->last_try_catch = 0;

	op_array->fn_flags = 0;

	op_array->last_literal = 0;
	op_array->literals = NULL;

	ZEND_MAP_PTR_INIT(op_array->run_time_cache, NULL);
	op_array->cache_size = zend_op_array_extension_handles * sizeof(void*);

	memset(op_array->reserved, 0, ZEND_MAX_RESERVED_RESOURCES * sizeof(void*));
}
