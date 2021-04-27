/* context extension for PHP */

#ifndef PHP_CONTEXT_H
# define PHP_CONTEXT_H

#if PHP_MAJOR_VERSION < 8
#error Minimum major version: 8.
#endif

extern zend_module_entry context_module_entry;
# define phpext_context_ptr &context_module_entry

# define PHP_CONTEXT_VERSION "0.1.0"

# if defined(ZTS) && defined(COMPILE_DL_CONTEXT)
ZEND_TSRMLS_CACHE_EXTERN()
# endif

#endif	/* PHP_CONTEXT_H */
