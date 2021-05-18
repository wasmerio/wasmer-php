/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 70154f1e75bf065443f1d7110d5c89b6bc02aca0 */

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_MemoryView_getI32, 0, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_MemoryView_setI32, 0, 2, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, value, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_class_Wasm_MemoryView_getI64 arginfo_class_Wasm_MemoryView_getI32

#define arginfo_class_Wasm_MemoryView_setI64 arginfo_class_Wasm_MemoryView_setI32

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_MemoryView_getF32, 0, 1, IS_DOUBLE, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_MemoryView_setF32, 0, 2, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, value, IS_DOUBLE, 0)
ZEND_END_ARG_INFO()

#define arginfo_class_Wasm_MemoryView_getF64 arginfo_class_Wasm_MemoryView_getF32

#define arginfo_class_Wasm_MemoryView_setF64 arginfo_class_Wasm_MemoryView_setF32


ZEND_METHOD(Wasm_MemoryView, getI32);
ZEND_METHOD(Wasm_MemoryView, setI32);
ZEND_METHOD(Wasm_MemoryView, getI64);
ZEND_METHOD(Wasm_MemoryView, setI64);
ZEND_METHOD(Wasm_MemoryView, getF32);
ZEND_METHOD(Wasm_MemoryView, setF32);
ZEND_METHOD(Wasm_MemoryView, getF64);
ZEND_METHOD(Wasm_MemoryView, setF64);


static const zend_function_entry class_Wasm_MemoryView_methods[] = {
	ZEND_ME(Wasm_MemoryView, getI32, arginfo_class_Wasm_MemoryView_getI32, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, setI32, arginfo_class_Wasm_MemoryView_setI32, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, getI64, arginfo_class_Wasm_MemoryView_getI64, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, setI64, arginfo_class_Wasm_MemoryView_setI64, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, getF32, arginfo_class_Wasm_MemoryView_getF32, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, setF32, arginfo_class_Wasm_MemoryView_setF32, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, getF64, arginfo_class_Wasm_MemoryView_getF64, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_MemoryView, setF64, arginfo_class_Wasm_MemoryView_setF64, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};
