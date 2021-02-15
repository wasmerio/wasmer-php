/* This is a generated file, edit the .stub.php file instead.
 * Stub hash: 7f167fdc4253fe8ac4a53d5e15d34c8d2421a2e6 */

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

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_functype_new, 0, 0, 2)
	ZEND_ARG_OBJ_INFO(0, params, Wasm\\Vec\\ValType, 0)
	ZEND_ARG_OBJ_INFO(0, results, Wasm\\Vec\\ValType, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_functype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, functype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_functype_params, 0, 1, Wasm\\Vec\\ValType, 0)
	ZEND_ARG_INFO(0, functype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_functype_results arginfo_wasm_functype_params

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_functype_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, functype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_functype_as_externtype arginfo_wasm_functype_copy

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_globaltype_new, 0, 0, 2)
	ZEND_ARG_INFO(0, valtype)
	ZEND_ARG_TYPE_INFO(0, mutability, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_globaltype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, globaltype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_globaltype_content, 0, 0, 1)
	ZEND_ARG_INFO(0, globaltype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_globaltype_mutability, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, globaltype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_globaltype_copy arginfo_wasm_globaltype_content

#define arginfo_wasm_globaltype_as_externtype arginfo_wasm_globaltype_content

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_limits_new, 0, 0, 2)
	ZEND_ARG_TYPE_INFO(0, min, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, max, IS_LONG, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_limits_min, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, limits)
ZEND_END_ARG_INFO()

#define arginfo_wasm_limits_max arginfo_wasm_limits_min

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_tabletype_new, 0, 0, 2)
	ZEND_ARG_INFO(0, valtype)
	ZEND_ARG_INFO(0, limits)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_tabletype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, tabletype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_tabletype_element, 0, 0, 1)
	ZEND_ARG_INFO(0, tabletype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_tabletype_limits arginfo_wasm_tabletype_element

#define arginfo_wasm_tabletype_copy arginfo_wasm_tabletype_element

#define arginfo_wasm_tabletype_as_externtype arginfo_wasm_tabletype_element

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_memorytype_new, 0, 0, 1)
	ZEND_ARG_INFO(0, limits)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_memorytype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, memorytype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_memorytype_limits, 0, 0, 1)
	ZEND_ARG_INFO(0, memorytype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_memorytype_copy arginfo_wasm_memorytype_limits

#define arginfo_wasm_memorytype_as_externtype arginfo_wasm_memorytype_limits

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_externtype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, externtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_externtype_kind, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, externtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_externtype_as_functype, 0, 0, 1)
	ZEND_ARG_INFO(0, externtype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_externtype_as_globaltype arginfo_wasm_externtype_as_functype

#define arginfo_wasm_externtype_as_tabletype arginfo_wasm_externtype_as_functype

#define arginfo_wasm_externtype_as_memorytype arginfo_wasm_externtype_as_functype

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_importtype_new, 0, 0, 3)
	ZEND_ARG_TYPE_INFO(0, module, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, name, IS_STRING, 0)
	ZEND_ARG_INFO(0, externtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_importtype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, importtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_importtype_module, 0, 1, IS_STRING, 0)
	ZEND_ARG_INFO(0, importtype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_importtype_name arginfo_wasm_importtype_module

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_importtype_type, 0, 0, 1)
	ZEND_ARG_INFO(0, importtype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_importtype_copy arginfo_wasm_importtype_type

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_exporttype_new, 0, 0, 2)
	ZEND_ARG_TYPE_INFO(0, name, IS_STRING, 0)
	ZEND_ARG_INFO(0, externtype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_exporttype_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, exporttype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_exporttype_name, 0, 1, IS_STRING, 0)
	ZEND_ARG_INFO(0, exporttype)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_exporttype_type, 0, 0, 1)
	ZEND_ARG_INFO(0, exporttype)
ZEND_END_ARG_INFO()

#define arginfo_wasm_exporttype_copy arginfo_wasm_exporttype_type

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_val_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_val_value, 0, 1, IS_MIXED, 0)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_val_kind, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_val_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_val_i32, 0, 0, 1)
	ZEND_ARG_TYPE_INFO(0, val, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasm_val_i64 arginfo_wasm_val_i32

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_val_f32, 0, 0, 1)
	ZEND_ARG_TYPE_INFO(0, val, IS_DOUBLE, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasm_val_f64 arginfo_wasm_val_f32

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_frame_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, frame)
ZEND_END_ARG_INFO()

#define arginfo_wasm_frame_instance arginfo_wasm_frame_copy

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_frame_func_index, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, frame)
ZEND_END_ARG_INFO()

#define arginfo_wasm_frame_func_offset arginfo_wasm_frame_func_index

#define arginfo_wasm_frame_module_offset arginfo_wasm_frame_func_index

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_trap_new, 0, 0, 2)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_TYPE_INFO(0, message, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_trap_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, trap)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_trap_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, trap)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_trap_message, 0, 1, IS_STRING, 0)
	ZEND_ARG_INFO(0, trap)
ZEND_END_ARG_INFO()

#define arginfo_wasm_trap_origin arginfo_wasm_trap_copy

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_trap_trace, 0, 1, Wasm\\Vec\\Frame, 0)
	ZEND_ARG_INFO(0, trap)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_module_new, 0, 0, 2)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_TYPE_INFO(0, wasm, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, module)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_validate, 0, 2, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_TYPE_INFO(0, wasm, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_module_imports, 0, 1, Wasm\\Vec\\ImportType, 0)
	ZEND_ARG_INFO(0, module)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_module_exports, 0, 1, Wasm\\Vec\\ExportType, 0)
	ZEND_ARG_INFO(0, module)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_serialize, 0, 1, IS_STRING, 0)
	ZEND_ARG_INFO(0, module)
ZEND_END_ARG_INFO()

#define arginfo_wasm_module_deserialize arginfo_wasm_module_new

#define arginfo_wasm_module_name arginfo_wasm_module_serialize

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_set_name, 0, 2, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, module)
	ZEND_ARG_TYPE_INFO(0, name, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_module_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, module)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_func_new, 0, 0, 3)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_INFO(0, functype)
	ZEND_ARG_TYPE_INFO(0, func, IS_CALLABLE, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_func_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, func)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_func_type, 0, 0, 1)
	ZEND_ARG_INFO(0, func)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_func_param_arity, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, func)
ZEND_END_ARG_INFO()

#define arginfo_wasm_func_result_arity arginfo_wasm_func_param_arity

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_func_call, 0, 2, Wasm\\Vec\\Val, 0)
	ZEND_ARG_INFO(0, func)
	ZEND_ARG_OBJ_INFO(0, args, Wasm\\Vec\\Val, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasm_func_as_extern arginfo_wasm_func_type

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_global_new, 0, 0, 3)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_INFO(0, globaltype)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_global_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, global)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_global_type, 0, 0, 1)
	ZEND_ARG_INFO(0, global)
ZEND_END_ARG_INFO()

#define arginfo_wasm_global_get arginfo_wasm_global_type

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_global_set, 0, 2, IS_VOID, 0)
	ZEND_ARG_INFO(0, global)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO()

#define arginfo_wasm_global_copy arginfo_wasm_global_type

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_global_same, 0, 2, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, left)
	ZEND_ARG_INFO(0, right)
ZEND_END_ARG_INFO()

#define arginfo_wasm_global_as_extern arginfo_wasm_global_type

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_extern_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, extern)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_extern_kind, 0, 1, IS_LONG, 0)
	ZEND_ARG_INFO(0, extern)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_extern_type, 0, 0, 1)
	ZEND_ARG_INFO(0, extern)
ZEND_END_ARG_INFO()

#define arginfo_wasm_extern_as_func arginfo_wasm_extern_type

#define arginfo_wasm_extern_as_global arginfo_wasm_extern_type

#define arginfo_wasm_extern_as_table arginfo_wasm_extern_type

#define arginfo_wasm_extern_as_memory arginfo_wasm_extern_type

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_instance_new, 0, 0, 3)
	ZEND_ARG_INFO(0, store)
	ZEND_ARG_INFO(0, module)
	ZEND_ARG_OBJ_INFO(0, externs, Wasm\\Vec\\Extern, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_instance_delete, 0, 1, _IS_BOOL, 0)
	ZEND_ARG_INFO(0, instance)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_instance_exports, 0, 1, Wasm\\Vec\\Extern, 0)
	ZEND_ARG_INFO(0, instance)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_wasm_instance_copy, 0, 0, 1)
	ZEND_ARG_INFO(0, instance)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version, 0, 0, IS_STRING, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmer_version_major, 0, 0, IS_LONG, 0)
ZEND_END_ARG_INFO()

#define arginfo_wasmer_version_minor arginfo_wasmer_version_major

#define arginfo_wasmer_version_patch arginfo_wasmer_version_major

#define arginfo_wasmer_version_pre arginfo_wasmer_version

ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wat2wasm, 0, 1, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, wat, IS_STRING, 0)
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
ZEND_FUNCTION(wasm_functype_new);
ZEND_FUNCTION(wasm_functype_delete);
ZEND_FUNCTION(wasm_functype_params);
ZEND_FUNCTION(wasm_functype_results);
ZEND_FUNCTION(wasm_functype_copy);
ZEND_FUNCTION(wasm_functype_as_externtype);
ZEND_FUNCTION(wasm_globaltype_new);
ZEND_FUNCTION(wasm_globaltype_delete);
ZEND_FUNCTION(wasm_globaltype_content);
ZEND_FUNCTION(wasm_globaltype_mutability);
ZEND_FUNCTION(wasm_globaltype_copy);
ZEND_FUNCTION(wasm_globaltype_as_externtype);
ZEND_FUNCTION(wasm_limits_new);
ZEND_FUNCTION(wasm_limits_min);
ZEND_FUNCTION(wasm_limits_max);
ZEND_FUNCTION(wasm_tabletype_new);
ZEND_FUNCTION(wasm_tabletype_delete);
ZEND_FUNCTION(wasm_tabletype_element);
ZEND_FUNCTION(wasm_tabletype_limits);
ZEND_FUNCTION(wasm_tabletype_copy);
ZEND_FUNCTION(wasm_tabletype_as_externtype);
ZEND_FUNCTION(wasm_memorytype_new);
ZEND_FUNCTION(wasm_memorytype_delete);
ZEND_FUNCTION(wasm_memorytype_limits);
ZEND_FUNCTION(wasm_memorytype_copy);
ZEND_FUNCTION(wasm_memorytype_as_externtype);
ZEND_FUNCTION(wasm_externtype_delete);
ZEND_FUNCTION(wasm_externtype_kind);
ZEND_FUNCTION(wasm_externtype_as_functype);
ZEND_FUNCTION(wasm_externtype_as_globaltype);
ZEND_FUNCTION(wasm_externtype_as_tabletype);
ZEND_FUNCTION(wasm_externtype_as_memorytype);
ZEND_FUNCTION(wasm_importtype_new);
ZEND_FUNCTION(wasm_importtype_delete);
ZEND_FUNCTION(wasm_importtype_module);
ZEND_FUNCTION(wasm_importtype_name);
ZEND_FUNCTION(wasm_importtype_type);
ZEND_FUNCTION(wasm_importtype_copy);
ZEND_FUNCTION(wasm_exporttype_new);
ZEND_FUNCTION(wasm_exporttype_delete);
ZEND_FUNCTION(wasm_exporttype_name);
ZEND_FUNCTION(wasm_exporttype_type);
ZEND_FUNCTION(wasm_exporttype_copy);
ZEND_FUNCTION(wasm_val_delete);
ZEND_FUNCTION(wasm_val_value);
ZEND_FUNCTION(wasm_val_kind);
ZEND_FUNCTION(wasm_val_copy);
ZEND_FUNCTION(wasm_val_i32);
ZEND_FUNCTION(wasm_val_i64);
ZEND_FUNCTION(wasm_val_f32);
ZEND_FUNCTION(wasm_val_f64);
ZEND_FUNCTION(wasm_frame_copy);
ZEND_FUNCTION(wasm_frame_instance);
ZEND_FUNCTION(wasm_frame_func_index);
ZEND_FUNCTION(wasm_frame_func_offset);
ZEND_FUNCTION(wasm_frame_module_offset);
ZEND_FUNCTION(wasm_trap_new);
ZEND_FUNCTION(wasm_trap_delete);
ZEND_FUNCTION(wasm_trap_copy);
ZEND_FUNCTION(wasm_trap_message);
ZEND_FUNCTION(wasm_trap_origin);
ZEND_FUNCTION(wasm_trap_trace);
ZEND_FUNCTION(wasm_module_new);
ZEND_FUNCTION(wasm_module_delete);
ZEND_FUNCTION(wasm_module_validate);
ZEND_FUNCTION(wasm_module_imports);
ZEND_FUNCTION(wasm_module_exports);
ZEND_FUNCTION(wasm_module_serialize);
ZEND_FUNCTION(wasm_module_deserialize);
ZEND_FUNCTION(wasm_module_name);
ZEND_FUNCTION(wasm_module_set_name);
ZEND_FUNCTION(wasm_module_copy);
ZEND_FUNCTION(wasm_func_new);
ZEND_FUNCTION(wasm_func_delete);
ZEND_FUNCTION(wasm_func_type);
ZEND_FUNCTION(wasm_func_param_arity);
ZEND_FUNCTION(wasm_func_result_arity);
ZEND_FUNCTION(wasm_func_call);
ZEND_FUNCTION(wasm_func_as_extern);
ZEND_FUNCTION(wasm_global_new);
ZEND_FUNCTION(wasm_global_delete);
ZEND_FUNCTION(wasm_global_type);
ZEND_FUNCTION(wasm_global_get);
ZEND_FUNCTION(wasm_global_set);
ZEND_FUNCTION(wasm_global_copy);
ZEND_FUNCTION(wasm_global_same);
ZEND_FUNCTION(wasm_global_as_extern);
ZEND_FUNCTION(wasm_extern_delete);
ZEND_FUNCTION(wasm_extern_kind);
ZEND_FUNCTION(wasm_extern_type);
ZEND_FUNCTION(wasm_extern_as_func);
ZEND_FUNCTION(wasm_extern_as_global);
ZEND_FUNCTION(wasm_extern_as_table);
ZEND_FUNCTION(wasm_extern_as_memory);
ZEND_FUNCTION(wasm_instance_new);
ZEND_FUNCTION(wasm_instance_delete);
ZEND_FUNCTION(wasm_instance_exports);
ZEND_FUNCTION(wasm_instance_copy);
ZEND_FUNCTION(wasmer_version);
ZEND_FUNCTION(wasmer_version_major);
ZEND_FUNCTION(wasmer_version_minor);
ZEND_FUNCTION(wasmer_version_patch);
ZEND_FUNCTION(wasmer_version_pre);
ZEND_FUNCTION(wat2wasm);


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
	ZEND_FE(wasm_functype_new, arginfo_wasm_functype_new)
	ZEND_FE(wasm_functype_delete, arginfo_wasm_functype_delete)
	ZEND_FE(wasm_functype_params, arginfo_wasm_functype_params)
	ZEND_FE(wasm_functype_results, arginfo_wasm_functype_results)
	ZEND_FE(wasm_functype_copy, arginfo_wasm_functype_copy)
	ZEND_FE(wasm_functype_as_externtype, arginfo_wasm_functype_as_externtype)
	ZEND_FE(wasm_globaltype_new, arginfo_wasm_globaltype_new)
	ZEND_FE(wasm_globaltype_delete, arginfo_wasm_globaltype_delete)
	ZEND_FE(wasm_globaltype_content, arginfo_wasm_globaltype_content)
	ZEND_FE(wasm_globaltype_mutability, arginfo_wasm_globaltype_mutability)
	ZEND_FE(wasm_globaltype_copy, arginfo_wasm_globaltype_copy)
	ZEND_FE(wasm_globaltype_as_externtype, arginfo_wasm_globaltype_as_externtype)
	ZEND_FE(wasm_limits_new, arginfo_wasm_limits_new)
	ZEND_FE(wasm_limits_min, arginfo_wasm_limits_min)
	ZEND_FE(wasm_limits_max, arginfo_wasm_limits_max)
	ZEND_FE(wasm_tabletype_new, arginfo_wasm_tabletype_new)
	ZEND_FE(wasm_tabletype_delete, arginfo_wasm_tabletype_delete)
	ZEND_FE(wasm_tabletype_element, arginfo_wasm_tabletype_element)
	ZEND_FE(wasm_tabletype_limits, arginfo_wasm_tabletype_limits)
	ZEND_FE(wasm_tabletype_copy, arginfo_wasm_tabletype_copy)
	ZEND_FE(wasm_tabletype_as_externtype, arginfo_wasm_tabletype_as_externtype)
	ZEND_FE(wasm_memorytype_new, arginfo_wasm_memorytype_new)
	ZEND_FE(wasm_memorytype_delete, arginfo_wasm_memorytype_delete)
	ZEND_FE(wasm_memorytype_limits, arginfo_wasm_memorytype_limits)
	ZEND_FE(wasm_memorytype_copy, arginfo_wasm_memorytype_copy)
	ZEND_FE(wasm_memorytype_as_externtype, arginfo_wasm_memorytype_as_externtype)
	ZEND_FE(wasm_externtype_delete, arginfo_wasm_externtype_delete)
	ZEND_FE(wasm_externtype_kind, arginfo_wasm_externtype_kind)
	ZEND_FE(wasm_externtype_as_functype, arginfo_wasm_externtype_as_functype)
	ZEND_FE(wasm_externtype_as_globaltype, arginfo_wasm_externtype_as_globaltype)
	ZEND_FE(wasm_externtype_as_tabletype, arginfo_wasm_externtype_as_tabletype)
	ZEND_FE(wasm_externtype_as_memorytype, arginfo_wasm_externtype_as_memorytype)
	ZEND_FE(wasm_importtype_new, arginfo_wasm_importtype_new)
	ZEND_FE(wasm_importtype_delete, arginfo_wasm_importtype_delete)
	ZEND_FE(wasm_importtype_module, arginfo_wasm_importtype_module)
	ZEND_FE(wasm_importtype_name, arginfo_wasm_importtype_name)
	ZEND_FE(wasm_importtype_type, arginfo_wasm_importtype_type)
	ZEND_FE(wasm_importtype_copy, arginfo_wasm_importtype_copy)
	ZEND_FE(wasm_exporttype_new, arginfo_wasm_exporttype_new)
	ZEND_FE(wasm_exporttype_delete, arginfo_wasm_exporttype_delete)
	ZEND_FE(wasm_exporttype_name, arginfo_wasm_exporttype_name)
	ZEND_FE(wasm_exporttype_type, arginfo_wasm_exporttype_type)
	ZEND_FE(wasm_exporttype_copy, arginfo_wasm_exporttype_copy)
	ZEND_FE(wasm_val_delete, arginfo_wasm_val_delete)
	ZEND_FE(wasm_val_value, arginfo_wasm_val_value)
	ZEND_FE(wasm_val_kind, arginfo_wasm_val_kind)
	ZEND_FE(wasm_val_copy, arginfo_wasm_val_copy)
	ZEND_FE(wasm_val_i32, arginfo_wasm_val_i32)
	ZEND_FE(wasm_val_i64, arginfo_wasm_val_i64)
	ZEND_FE(wasm_val_f32, arginfo_wasm_val_f32)
	ZEND_FE(wasm_val_f64, arginfo_wasm_val_f64)
	ZEND_FE(wasm_frame_copy, arginfo_wasm_frame_copy)
	ZEND_FE(wasm_frame_instance, arginfo_wasm_frame_instance)
	ZEND_FE(wasm_frame_func_index, arginfo_wasm_frame_func_index)
	ZEND_FE(wasm_frame_func_offset, arginfo_wasm_frame_func_offset)
	ZEND_FE(wasm_frame_module_offset, arginfo_wasm_frame_module_offset)
	ZEND_FE(wasm_trap_new, arginfo_wasm_trap_new)
	ZEND_FE(wasm_trap_delete, arginfo_wasm_trap_delete)
	ZEND_FE(wasm_trap_copy, arginfo_wasm_trap_copy)
	ZEND_FE(wasm_trap_message, arginfo_wasm_trap_message)
	ZEND_FE(wasm_trap_origin, arginfo_wasm_trap_origin)
	ZEND_FE(wasm_trap_trace, arginfo_wasm_trap_trace)
	ZEND_FE(wasm_module_new, arginfo_wasm_module_new)
	ZEND_FE(wasm_module_delete, arginfo_wasm_module_delete)
	ZEND_FE(wasm_module_validate, arginfo_wasm_module_validate)
	ZEND_FE(wasm_module_imports, arginfo_wasm_module_imports)
	ZEND_FE(wasm_module_exports, arginfo_wasm_module_exports)
	ZEND_FE(wasm_module_serialize, arginfo_wasm_module_serialize)
	ZEND_FE(wasm_module_deserialize, arginfo_wasm_module_deserialize)
	ZEND_FE(wasm_module_name, arginfo_wasm_module_name)
	ZEND_FE(wasm_module_set_name, arginfo_wasm_module_set_name)
	ZEND_FE(wasm_module_copy, arginfo_wasm_module_copy)
	ZEND_FE(wasm_func_new, arginfo_wasm_func_new)
	ZEND_FE(wasm_func_delete, arginfo_wasm_func_delete)
	ZEND_FE(wasm_func_type, arginfo_wasm_func_type)
	ZEND_FE(wasm_func_param_arity, arginfo_wasm_func_param_arity)
	ZEND_FE(wasm_func_result_arity, arginfo_wasm_func_result_arity)
	ZEND_FE(wasm_func_call, arginfo_wasm_func_call)
	ZEND_FE(wasm_func_as_extern, arginfo_wasm_func_as_extern)
	ZEND_FE(wasm_global_new, arginfo_wasm_global_new)
	ZEND_FE(wasm_global_delete, arginfo_wasm_global_delete)
	ZEND_FE(wasm_global_type, arginfo_wasm_global_type)
	ZEND_FE(wasm_global_get, arginfo_wasm_global_get)
	ZEND_FE(wasm_global_set, arginfo_wasm_global_set)
	ZEND_FE(wasm_global_copy, arginfo_wasm_global_copy)
	ZEND_FE(wasm_global_same, arginfo_wasm_global_same)
	ZEND_FE(wasm_global_as_extern, arginfo_wasm_global_as_extern)
	ZEND_FE(wasm_extern_delete, arginfo_wasm_extern_delete)
	ZEND_FE(wasm_extern_kind, arginfo_wasm_extern_kind)
	ZEND_FE(wasm_extern_type, arginfo_wasm_extern_type)
	ZEND_FE(wasm_extern_as_func, arginfo_wasm_extern_as_func)
	ZEND_FE(wasm_extern_as_global, arginfo_wasm_extern_as_global)
	ZEND_FE(wasm_extern_as_table, arginfo_wasm_extern_as_table)
	ZEND_FE(wasm_extern_as_memory, arginfo_wasm_extern_as_memory)
	ZEND_FE(wasm_instance_new, arginfo_wasm_instance_new)
	ZEND_FE(wasm_instance_delete, arginfo_wasm_instance_delete)
	ZEND_FE(wasm_instance_exports, arginfo_wasm_instance_exports)
	ZEND_FE(wasm_instance_copy, arginfo_wasm_instance_copy)
	ZEND_FE(wasmer_version, arginfo_wasmer_version)
	ZEND_FE(wasmer_version_major, arginfo_wasmer_version_major)
	ZEND_FE(wasmer_version_minor, arginfo_wasmer_version_minor)
	ZEND_FE(wasmer_version_patch, arginfo_wasmer_version_patch)
	ZEND_FE(wasmer_version_pre, arginfo_wasmer_version_pre)
	ZEND_FE(wat2wasm, arginfo_wat2wasm)
	ZEND_FE_END
};
