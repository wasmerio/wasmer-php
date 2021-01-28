#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "Zend/zend_interfaces.h"

#include "wasmer_wasm.h"

#include "php_wasmer.h"
#include "wasmer_arginfo.h"
#include "macros.h"
#include "wasmer.h"

///////////////////////////////////////////////////////////////////////////////
// Runtime Environment

WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(config)
static ZEND_RSRC_DTOR_FUNC(wasm_config_dtor) {
    // TODO(jubianchi): Add call to wasm_config_delete (see https://github.com/wasmerio/wasmer/pull/2054)
    efree(res->ptr);
}
WASMER_RESOURCE_DECLARE(engine)
WASMER_RESOURCE_DECLARE(store)

///////////////////////////////////////////////////////////////////////////////
// Type Representations

WASMER_RESOURCE_DECLARE(valtype)
WASMER_VEC_CLASS_ENTRY_DECLARE(valtype)
WASMER_RESOURCE_DECLARE(functype)
WASMER_VEC_CLASS_ENTRY_DECLARE(functype)
WASMER_RESOURCE_DECLARE(globaltype)
WASMER_VEC_CLASS_ENTRY_DECLARE(globaltype)
WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(limits)
static ZEND_RSRC_DTOR_FUNC(wasm_limits_dtor) {
    efree(res->ptr);
}
WASMER_RESOURCE_DECLARE(tabletype)
WASMER_VEC_CLASS_ENTRY_DECLARE(tabletype)
WASMER_RESOURCE_DECLARE(memorytype)
WASMER_VEC_CLASS_ENTRY_DECLARE(memorytype)
WASMER_RESOURCE_DECLARE(externtype)
WASMER_VEC_CLASS_ENTRY_DECLARE(externtype)
WASMER_RESOURCE_DECLARE(importtype)
WASMER_VEC_CLASS_ENTRY_DECLARE(importtype)
WASMER_RESOURCE_DECLARE(exporttype)
WASMER_VEC_CLASS_ENTRY_DECLARE(exporttype)

///////////////////////////////////////////////////////////////////////////////
// Runtime Objects

///////////////////////////////////////////////////////////////////////////////

PHP_MINIT_FUNCTION(wasmer) {
    zend_class_entry ce;

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Environment

    WASMER_RESOURCE_REGISTER(config)
    WASMER_RESOURCE_REGISTER(engine)
    WASMER_RESOURCE_REGISTER(store)

    ///////////////////////////////////////////////////////////////////////////////
    // Type Representations

    WASMER_RESOURCE_REGISTER(valtype)
    WASMER_VEC_CLASS_REGISTER(ValType, valtype)
    WASMER_RESOURCE_REGISTER(functype)
    WASMER_VEC_CLASS_REGISTER(FuncType, functype)
    WASMER_RESOURCE_REGISTER(globaltype)
    WASMER_VEC_CLASS_REGISTER(GlobalType, globaltype)
    WASMER_RESOURCE_REGISTER(limits)
    WASMER_RESOURCE_REGISTER(tabletype)
    WASMER_VEC_CLASS_REGISTER(TableType, tabletype)
    WASMER_RESOURCE_REGISTER(memorytype)
    WASMER_VEC_CLASS_REGISTER(MemoryType, memorytype)
    WASMER_RESOURCE_REGISTER(externtype)
    WASMER_VEC_CLASS_REGISTER(ExternType, externtype)
    WASMER_RESOURCE_REGISTER(importtype)
    WASMER_VEC_CLASS_REGISTER(ImportType, importtype)
    WASMER_RESOURCE_REGISTER(exporttype)
    WASMER_VEC_CLASS_REGISTER(ExportType, exporttype)

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Objects

    ///////////////////////////////////////////////////////////////////////////////
    // Type Representations

    // Engines
    REGISTER_LONG_CONSTANT("WASM_ENGINE_JIT", JIT, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_ENGINE_NATIVE", NATIVE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_ENGINE_OBJECT_FILE", OBJECT_FILE, CONST_CS | CONST_PERSISTENT);

    // Compilers
    REGISTER_LONG_CONSTANT("WASM_COMPILER_CRANELIFT", CRANELIFT, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_COMPILER_LLVM", LLVM, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_COMPILER_SINGLEPASS", SINGLEPASS, CONST_CS | CONST_PERSISTENT);

    // Type attributes
    REGISTER_LONG_CONSTANT("WASM_CONST", WASM_CONST, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_VAR", WASM_VAR, CONST_CS | CONST_PERSISTENT);

    // Value Types
    REGISTER_LONG_CONSTANT("WASM_I32", WASM_I32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_I64", WASM_I64, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_F32", WASM_F32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_F64", WASM_F64, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_ANYREF", WASM_ANYREF, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_FUNCREF", WASM_FUNCREF, CONST_CS | CONST_PERSISTENT);

    // Extern Types
    REGISTER_LONG_CONSTANT("WASM_EXTERN_FUNC", WASM_EXTERN_FUNC, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_EXTERN_GLOBAL", WASM_EXTERN_GLOBAL, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_EXTERN_TABLE", WASM_EXTERN_TABLE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_EXTERN_MEMORY", WASM_EXTERN_MEMORY, CONST_CS | CONST_PERSISTENT);

    REGISTER_LONG_CONSTANT("WASM_LIMITS_MAX_DEFAULT", wasm_limits_max_default, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_MEMORY_PAGE_SIZE", MEMORY_PAGE_SIZE, CONST_CS | CONST_PERSISTENT);

    return SUCCESS;
}

PHP_RINIT_FUNCTION(wasmer) {
#if defined(ZTS) && defined(COMPILE_DL_WASMER)
  ZEND_TSRMLS_CACHE_UPDATE();
#endif

  return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(wasmer) { return SUCCESS; }

PHP_MSHUTDOWN_FUNCTION(wasmer) { return SUCCESS; }

PHP_MINFO_FUNCTION(wasmer) {
  php_info_print_table_start();
  php_info_print_table_end();
}

zend_module_entry wasmer_module_entry = {
    STANDARD_MODULE_HEADER, "wasmer",
    ext_functions,          PHP_MINIT(wasmer),
    PHP_MSHUTDOWN(wasmer),  PHP_RINIT(wasmer),
    PHP_RSHUTDOWN(wasmer),  PHP_MINFO(wasmer),
    PHP_WASMER_VERSION,     STANDARD_MODULE_PROPERTIES};

#ifdef COMPILE_DL_WASMER
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(wasmer)
#endif