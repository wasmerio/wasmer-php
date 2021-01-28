/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 66acdf87a44fc51233039a77724f2fb459ec407c */

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_config_new, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_config_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, config)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_config_set_compiler, 0, 2, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, config)
	ZEND_ARG_TYPE_INFO(0, compiler, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_config_set_engine, 0, 2, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, config)
	ZEND_ARG_TYPE_INFO(0, engine, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasm_engine_new arginfo_wasm_config_new

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_engine_new_with_config, 0, 0, 1)
	ZEND_ARG_INFO(0, config)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_engine_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, engine)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_store_new, 0, 0, 1)
	ZEND_ARG_INFO(0, engine)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_store_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, store)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_valtype_new, 0, 0, 1)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_valtype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, valtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_valtype_kind, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, valtype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_valtype_is_num arginfo_wasm_valtype_delete

#define arginfo_wasm_valtype_is_ref arginfo_wasm_valtype_delete

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_valtype_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, valtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_valkind_is_num, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_TYPE_INFO(0, kind, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasm_valkind_is_ref arginfo_wasm_valkind_is_num

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_limits_new, 0, 0, 2)
	ZEND_ARG_TYPE_INFO(0, min, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, max, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_limits_min, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, limits)
ZEND_END_ARG_INFO()

#define arginfo_wasm_limits_max arginfo_wasm_limits_min

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version, 0, 0, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version_major, 0, 0, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasmer_version_minor arginfo_wasmer_version_major

#define arginfo_wasmer_version_patch arginfo_wasmer_version_major

#define arginfo_wasmer_version_pre arginfo_wasmer_version

ZEND_BEGIN_ARG_INFO_EX(arginfo_class_Wasm_Vec_ValType___construct, 0, 0, 0)
	ZEND_ARG_TYPE_MASK(0, sizeOrValtypes, MAY_BE_ARRAY|MAY_BE_LONG|MAY_BE_NULL, "null")
ZEND_END_ARG_INFO()

#define arginfo_class_Wasm_Vec_ValType_count arginfo_wasmer_version_major

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_Vec_ValType_offsetExists, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_Vec_ValType_offsetGet, 0, 1, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_Vec_ValType_offsetSet, 0, 2, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
	ZEND_ARG_TYPE_INFO(0, value, IS_MIXED, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_class_Wasm_Vec_ValType_offsetUnset, 0, 1, IS_VOID, 0)
	ZEND_ARG_TYPE_INFO(0, offset, IS_MIXED, 0)
ZEND_END_ARG_INFO()


ZEND_FUNCTION(wasm_config_new);
ZEND_FUNCTION(wasm_config_delete);
ZEND_FUNCTION(wasm_config_set_compiler);
ZEND_FUNCTION(wasm_config_set_engine);
ZEND_FUNCTION(wasm_engine_new);
ZEND_FUNCTION(wasm_engine_new_with_config);
ZEND_FUNCTION(wasm_engine_delete);
ZEND_FUNCTION(wasm_store_new);
ZEND_FUNCTION(wasm_store_delete);
ZEND_FUNCTION(wasm_valtype_new);
ZEND_FUNCTION(wasm_valtype_delete);
ZEND_FUNCTION(wasm_valtype_kind);
ZEND_FUNCTION(wasm_valtype_is_num);
ZEND_FUNCTION(wasm_valtype_is_ref);
ZEND_FUNCTION(wasm_valtype_copy);
ZEND_FUNCTION(wasm_valkind_is_num);
ZEND_FUNCTION(wasm_valkind_is_ref);
ZEND_FUNCTION(wasm_limits_new);
ZEND_FUNCTION(wasm_limits_min);
ZEND_FUNCTION(wasm_limits_max);
ZEND_FUNCTION(wasmer_version);
ZEND_FUNCTION(wasmer_version_major);
ZEND_FUNCTION(wasmer_version_minor);
ZEND_FUNCTION(wasmer_version_patch);
ZEND_FUNCTION(wasmer_version_pre);
ZEND_METHOD(Wasm_Vec_ValType, __construct);
ZEND_METHOD(Wasm_Vec_ValType, count);
ZEND_METHOD(Wasm_Vec_ValType, offsetExists);
ZEND_METHOD(Wasm_Vec_ValType, offsetGet);
ZEND_METHOD(Wasm_Vec_ValType, offsetSet);
ZEND_METHOD(Wasm_Vec_ValType, offsetUnset);


static const zend_function_entry ext_functions[] = {
	ZEND_FE(wasm_config_new, arginfo_wasm_config_new)
	ZEND_FE(wasm_config_delete, arginfo_wasm_config_delete)
	ZEND_FE(wasm_config_set_compiler, arginfo_wasm_config_set_compiler)
	ZEND_FE(wasm_config_set_engine, arginfo_wasm_config_set_engine)
	ZEND_FE(wasm_engine_new, arginfo_wasm_engine_new)
	ZEND_FE(wasm_engine_new_with_config, arginfo_wasm_engine_new_with_config)
	ZEND_FE(wasm_engine_delete, arginfo_wasm_engine_delete)
	ZEND_FE(wasm_store_new, arginfo_wasm_store_new)
	ZEND_FE(wasm_store_delete, arginfo_wasm_store_delete)
	ZEND_FE(wasm_valtype_new, arginfo_wasm_valtype_new)
	ZEND_FE(wasm_valtype_delete, arginfo_wasm_valtype_delete)
	ZEND_FE(wasm_valtype_kind, arginfo_wasm_valtype_kind)
	ZEND_FE(wasm_valtype_is_num, arginfo_wasm_valtype_is_num)
	ZEND_FE(wasm_valtype_is_ref, arginfo_wasm_valtype_is_ref)
	ZEND_FE(wasm_valtype_copy, arginfo_wasm_valtype_copy)
	ZEND_FE(wasm_valkind_is_num, arginfo_wasm_valkind_is_num)
	ZEND_FE(wasm_valkind_is_ref, arginfo_wasm_valkind_is_ref)
	ZEND_FE(wasm_limits_new, arginfo_wasm_limits_new)
	ZEND_FE(wasm_limits_min, arginfo_wasm_limits_min)
	ZEND_FE(wasm_limits_max, arginfo_wasm_limits_max)
	ZEND_FE(wasmer_version, arginfo_wasmer_version)
	ZEND_FE(wasmer_version_major, arginfo_wasmer_version_major)
	ZEND_FE(wasmer_version_minor, arginfo_wasmer_version_minor)
	ZEND_FE(wasmer_version_patch, arginfo_wasmer_version_patch)
	ZEND_FE(wasmer_version_pre, arginfo_wasmer_version_pre)
	ZEND_FE_END
};


static const zend_function_entry class_Wasm_Vec_ValType_methods[] = {
	ZEND_ME(Wasm_Vec_ValType, __construct, arginfo_class_Wasm_Vec_ValType___construct, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_Vec_ValType, count, arginfo_class_Wasm_Vec_ValType_count, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_Vec_ValType, offsetExists, arginfo_class_Wasm_Vec_ValType_offsetExists, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_Vec_ValType, offsetGet, arginfo_class_Wasm_Vec_ValType_offsetGet, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_Vec_ValType, offsetSet, arginfo_class_Wasm_Vec_ValType_offsetSet, ZEND_ACC_PUBLIC)
	ZEND_ME(Wasm_Vec_ValType, offsetUnset, arginfo_class_Wasm_Vec_ValType_offsetUnset, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};
