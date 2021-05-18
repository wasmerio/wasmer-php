#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "Zend/zend_interfaces.h"
#include "Zend/zend_exceptions.h"
#include <ext/spl/spl_exceptions.h>

#include "wasmer_wasm.h"

#include "php_wasm.h"
#include "wasmer_exception_arginfo.h"
#include "wasmer_root_arginfo.h"
#include "wasmer_vec_arginfo.h"
#include "wasmer_class_arginfo.h"
#include "macros.h"
#include "wasm.h"

zend_class_entry *wasm_exception_runtime_ce;
zend_class_entry *wasm_exception_instantiation_ce;
zend_class_entry *wasm_exception_oob_ce;


///////////////////////////////////////////////////////////////////////////////
// Runtime Environment

WASMER_RESOURCE_DECLARE(config)
WASMER_RESOURCE_DECLARE(engine)
WASMER_RESOURCE_DECLARE(store)

///////////////////////////////////////////////////////////////////////////////
// Type Representations

WASMER_RESOURCE_DECLARE(exporttype)
WASMER_VEC_CLASS_DECLARE(exporttype)
WASMER_RESOURCE_DECLARE(externtype)
WASMER_VEC_CLASS_DECLARE(externtype)
WASMER_RESOURCE_DECLARE(functype)
WASMER_VEC_CLASS_DECLARE(functype)
WASMER_RESOURCE_DECLARE(globaltype)
WASMER_VEC_CLASS_DECLARE(globaltype)
WASMER_RESOURCE_DECLARE(importtype)
WASMER_VEC_CLASS_DECLARE(importtype)
WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(limits)
static ZEND_RSRC_DTOR_FUNC(wasm_limits_dtor) {
    if (res->ptr != NULL) {
        efree(res->ptr);
    }
}
WASMER_RESOURCE_DECLARE(memorytype)
WASMER_VEC_CLASS_DECLARE(memorytype)
WASMER_RESOURCE_DECLARE(tabletype)
WASMER_VEC_CLASS_DECLARE(tabletype)
WASMER_RESOURCE_DECLARE(valtype)
WASMER_VEC_CLASS_DECLARE(valtype)

///////////////////////////////////////////////////////////////////////////////
// Runtime Objects

WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(extern)
static ZEND_RSRC_DTOR_FUNC(wasm_extern_dtor) {
    wasmer_res *extern_res = (wasmer_res*) res->ptr;
    wasm_extern_t *wasm_extern = extern_res->inner.xtern;

    if (extern_res->owned) {
        wasm_extern_delete(wasm_extern);
    }

    if (res->ptr != NULL) {
        efree(res->ptr);
    }
}
WASMER_VEC_CLASS_DECLARE_WITH_ALIAS(extern, xtern)
WASMER_RESOURCE_DECLARE(foreign)
WASMER_RESOURCE_DECLARE(frame)
WASMER_VEC_CLASS_DECLARE(frame)
WASMER_RESOURCE_DECLARE(func)
WASMER_RESOURCE_DECLARE(global)
WASMER_RESOURCE_DECLARE(instance)
WASMER_RESOURCE_DECLARE(memory)
WASMER_RESOURCE_DECLARE(module)
WASMER_RESOURCE_DECLARE(table)
WASMER_RESOURCE_DECLARE(trap)
WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(val)
static ZEND_RSRC_DTOR_FUNC(wasm_val_dtor) {
    wasmer_res *val_res = (wasmer_res*) res->ptr;
    wasm_val_t wasm_val = val_res->inner.val;

    if (val_res->owned) {
        wasm_val_delete(&wasm_val);
    }

    if (res->ptr != NULL) {
        efree(res->ptr);
    }
}
WASMER_VEC_CLASS_DECLARE(val)

zend_class_entry *wasm_memory_view_ce;
static zend_object_handlers wasm_memory_view_object_handlers;

zend_object *wasm_memory_view_create(zend_class_entry *ce) {
    wasm_memory_view_c *wasm_memory_view = zend_object_alloc(sizeof(wasm_memory_view_c), ce);

    zend_object_std_init(&wasm_memory_view->std, ce);
    wasm_memory_view->std.handlers = &wasm_memory_view_object_handlers;

    return &wasm_memory_view->std;
}

void wasm_memory_view_free(zend_object *object) {
    zend_object_std_dtor(object);
}

///////////////////////////////////////////////////////////////////////////////

PHP_MINIT_FUNCTION(wasm) {
    zend_class_entry ce;

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Environment

    WASMER_RESOURCE_REGISTER(config)
    WASMER_RESOURCE_REGISTER(engine)
    WASMER_RESOURCE_REGISTER(store)

    ///////////////////////////////////////////////////////////////////////////////
    // Type Representations

    WASMER_RESOURCE_REGISTER(exporttype)
    WASMER_VEC_CLASS_REGISTER(ExportType, exporttype)
    WASMER_RESOURCE_REGISTER(externtype)
    WASMER_VEC_CLASS_REGISTER(ExternType, externtype)
    WASMER_RESOURCE_REGISTER(functype)
    WASMER_VEC_CLASS_REGISTER(FuncType, functype)
    WASMER_RESOURCE_REGISTER(globaltype)
    WASMER_VEC_CLASS_REGISTER(GlobalType, globaltype)
    WASMER_RESOURCE_REGISTER(importtype)
    WASMER_VEC_CLASS_REGISTER(ImportType, importtype)
    WASMER_RESOURCE_REGISTER(limits)
    WASMER_RESOURCE_REGISTER(memorytype)
    WASMER_VEC_CLASS_REGISTER(MemoryType, memorytype)
    WASMER_RESOURCE_REGISTER(tabletype)
    WASMER_VEC_CLASS_REGISTER(TableType, tabletype)
    WASMER_RESOURCE_REGISTER(valtype)
    WASMER_VEC_CLASS_REGISTER(ValType, valtype)

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Objects

    WASMER_RESOURCE_REGISTER(extern)
    WASMER_VEC_CLASS_REGISTER(Extern, extern)
    WASMER_RESOURCE_REGISTER(foreign)
    WASMER_RESOURCE_REGISTER(frame)
    WASMER_VEC_CLASS_REGISTER(Frame, frame)
    WASMER_RESOURCE_REGISTER(func)
    WASMER_RESOURCE_REGISTER(global)
    WASMER_RESOURCE_REGISTER(instance)
    WASMER_RESOURCE_REGISTER(memory)
    WASMER_RESOURCE_REGISTER(module)
    // TODO(jubianchi): references
    WASMER_RESOURCE_REGISTER(table)
    WASMER_RESOURCE_REGISTER(trap)
    WASMER_RESOURCE_REGISTER(val)
    WASMER_VEC_CLASS_REGISTER(Val, val)

    INIT_NS_CLASS_ENTRY(ce, "Wasm", "MemoryView", class_Wasm_MemoryView_methods)
    wasm_memory_view_ce = zend_register_internal_class(&ce);
    wasm_memory_view_ce->ce_flags |= ZEND_ACC_FINAL;
    wasm_memory_view_ce->create_object = wasm_memory_view_create;

    memcpy(&wasm_memory_view_object_handlers, &std_object_handlers, sizeof(zend_object_handlers));
    wasm_memory_view_object_handlers.offset = XtOffsetOf(struct wasm_memory_view_c, std);
    wasm_memory_view_object_handlers.free_obj = wasm_memory_view_free;
    wasm_memory_view_object_handlers.clone_obj = NULL;

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

    INIT_NS_CLASS_ENTRY(ce, "Wasm\\Exception", "RuntimeException", class_Wasm_Exception_RuntimeException_methods);
    wasm_exception_runtime_ce = zend_register_internal_class_ex(&ce, spl_ce_RuntimeException);

    INIT_NS_CLASS_ENTRY(ce, "Wasm\\Exception", "InstantiationException", class_Wasm_Exception_InstantiationException_methods);
    wasm_exception_instantiation_ce = zend_register_internal_class_ex(&ce, wasm_exception_runtime_ce);

    INIT_NS_CLASS_ENTRY(ce, "Wasm\\Exception", "OutOfBoundsException", class_Wasm_Exception_OutOfBoundsException_methods);
    wasm_exception_oob_ce = zend_register_internal_class_ex(&ce, wasm_exception_runtime_ce);

    return SUCCESS;
}

PHP_MINFO_FUNCTION(wasm) {
    php_info_print_table_start();
    php_info_print_table_header(2, "Wasm support", "enabled");
    php_info_print_table_header(2, "Wasmer version", wasmer_version());
    php_info_print_table_end();
}

zend_module_entry wasm_module_entry = {
    STANDARD_MODULE_HEADER,
    "wasm",
    ext_functions,
    PHP_MINIT(wasm),
    NULL,
    NULL,
    NULL,
    PHP_MINFO(wasm),
    PHP_WASM_VERSION,
    STANDARD_MODULE_PROPERTIES};

#ifdef COMPILE_DL_WASM
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(wasm)
#endif