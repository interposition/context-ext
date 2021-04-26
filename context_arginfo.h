/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 644f8689168ed281b404a97088dca84491b76f91 */

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_Interposition_Context___construct, 0, 0, 1)
	ZEND_ARG_OBJ_INFO(0, closure, Closure, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Interposition_Context_resume, 0, 0, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO_WITH_DEFAULT_VALUE(0, value, IS_MIXED, 0, "null")
ZEND_END_ARG_INFO()

#define arginfo_class_Interposition_Context_suspend arginfo_class_Interposition_Context_resume

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Interposition_Context_finished, 0, 0, _IS_BOOL, 0)
ZEND_END_ARG_INFO()


ZEND_METHOD(Interposition_Context, __construct);
ZEND_METHOD(Interposition_Context, resume);
ZEND_METHOD(Interposition_Context, suspend);
ZEND_METHOD(Interposition_Context, finished);


static const zend_function_entry class_Interposition_Context_methods[] = {
	ZEND_ME(Interposition_Context, __construct, arginfo_class_Interposition_Context___construct, ZEND_ACC_PUBLIC)
	ZEND_ME(Interposition_Context, resume, arginfo_class_Interposition_Context_resume, ZEND_ACC_PUBLIC)
	ZEND_ME(Interposition_Context, suspend, arginfo_class_Interposition_Context_suspend, ZEND_ACC_PUBLIC|ZEND_ACC_STATIC)
	ZEND_ME(Interposition_Context, finished, arginfo_class_Interposition_Context_finished, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};
