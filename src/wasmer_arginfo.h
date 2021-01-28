/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: e3d72a2686fc4cdc77acb4a7bcad8fbc2dc9a5ea */

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

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version, 0, 0, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version_major, 0, 0, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasmer_version_minor arginfo_wasmer_version_major

#define arginfo_wasmer_version_patch arginfo_wasmer_version_major

#define arginfo_wasmer_version_pre arginfo_wasmer_version


ZEND_FUNCTION(wasm_config_new);
ZEND_FUNCTION(wasm_config_delete);
ZEND_FUNCTION(wasm_config_set_compiler);
ZEND_FUNCTION(wasm_config_set_engine);
ZEND_FUNCTION(wasm_engine_new);
ZEND_FUNCTION(wasm_engine_new_with_config);
ZEND_FUNCTION(wasm_engine_delete);
ZEND_FUNCTION(wasm_store_new);
ZEND_FUNCTION(wasm_store_delete);
ZEND_FUNCTION(wasmer_version);
ZEND_FUNCTION(wasmer_version_major);
ZEND_FUNCTION(wasmer_version_minor);
ZEND_FUNCTION(wasmer_version_patch);
ZEND_FUNCTION(wasmer_version_pre);


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
	ZEND_FE(wasmer_version, arginfo_wasmer_version)
	ZEND_FE(wasmer_version_major, arginfo_wasmer_version_major)
	ZEND_FE(wasmer_version_minor, arginfo_wasmer_version_minor)
	ZEND_FE(wasmer_version_patch, arginfo_wasmer_version_patch)
	ZEND_FE(wasmer_version_pre, arginfo_wasmer_version_pre)
	ZEND_FE_END
};
