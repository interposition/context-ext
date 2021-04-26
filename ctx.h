#include "php.h"

#ifndef CONTEXT_EXT_CTX_H
# define CONTEXT_EXT_CTX_H

typedef struct{
	zend_execute_data 	  * current_execute_data;
    zval          	      * vm_stack_top;
    zval          	  	  * vm_stack_end;
    zend_vm_stack  	    	vm_stack;
    size_t         	    	vm_stack_page_size;
    uint32_t jit_trace_num; /* Used by tracing JIT to reference the currently running trace */

} context;

context *create_context();
void free_context(context *ctx);
void push_function(context *ctx, zend_function * function, uint32_t call_info, 	void *object_or_called_scope);

# endif

