#include "terminate.h"
#include "zend_exceptions.h"
#include "init_op_array.h"
#include "zend_extensions.h"
#include "zend_compile.h"

#define CONTEXT_EXT_OPCODES_COUNT = 2;

static void init_op(zend_op * op, zend_uchar op_number)
{
	op->opcode 			= op_number;
    op->op1_type 		= IS_UNUSED;
	op->op2_type 		= IS_UNUSED;
	op->result_type 	= IS_UNUSED;

	zend_vm_set_opcode_handler(op);
}

static zend_uchar register_op(int (*handler)())
{
	zend_uchar op_number = ZEND_VM_LAST_OPCODE + 1;

	while(op_number < 255){

		if(zend_get_user_opcode_handler(op_number) != NULL) {
			op_number +=1;
			continue;
		}

		zend_set_user_opcode_handler(op_number, handler);

		return op_number;
	}

	return 0;
}

zend_function * register_terminate_func(int (*handler)())
{
	zend_uchar terminate_op = register_op(handler);

	if(terminate_op == 0){
		return NULL;
	}

	zend_op_array * op_array = pemalloc(sizeof(zend_op_array), 1);

	// if use 2 - segmentation fail, but in gdb all right
	_init_op_array(op_array, ZEND_USER_FUNCTION | ZEND_ACC_HEAP_RT_CACHE, 2);

	op_array->last_try_catch 		= 1;
    op_array->last             		= 2;

	op_array->try_catch_array				= pemalloc(sizeof(zend_try_catch_element), 1);
	op_array->try_catch_array->try_op 		= 0;
	op_array->try_catch_array->catch_op 	= 1;
	op_array->try_catch_array->finally_op 	= 0;
	op_array->try_catch_array->finally_end 	= 0;


	op_array->function_name 		= zend_string_init(ZEND_STRL("test"), 1);
	op_array->filename = ZSTR_EMPTY_ALLOC();

    init_op(op_array->opcodes, terminate_op);
    init_op((op_array->opcodes + 1), terminate_op);

    return (zend_function *)op_array;
}

// see init_op_array zend_opcode.c
void destroy_terminate_func(zend_function * func)
{
	zend_op_array * op_array = (zend_op_array *)func;

	zend_string_release(op_array->function_name);

	pefree(op_array->refcount, 1);
	pefree(op_array->opcodes, 1);
	pefree(op_array->try_catch_array, 1);
	pefree(op_array, 1);
}
