#include "php.h"

zend_function * register_terminate_func(int (*handler)());
void destroy_terminate_func(zend_function * func);
