#include "swap_context.h"

void set_context(context * ctx)
{
	EG(vm_stack) 			= ctx->vm_stack;
    EG(vm_stack_top)		= ctx->vm_stack_top;
    EG(vm_stack_end)		= ctx->vm_stack_end;
    EG(vm_stack_page_size)	= ctx->vm_stack_page_size;
    EG(current_execute_data)= ctx->current_execute_data;
    EG(jit_trace_num)		= ctx->jit_trace_num;
}

void save_context(context * ctx)
{
	ctx->vm_stack 				= EG(vm_stack);
    ctx->vm_stack_top 			= EG(vm_stack_top);
    ctx->vm_stack_end			= EG(vm_stack_end);
 	ctx->vm_stack_page_size 	= EG(vm_stack_page_size);
 	ctx->current_execute_data	= EG(current_execute_data);
 	ctx->jit_trace_num			= EG(jit_trace_num);
}
