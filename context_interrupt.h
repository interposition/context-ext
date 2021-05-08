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
