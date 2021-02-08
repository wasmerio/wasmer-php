#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_TYPE(ExternType, EXTERNTYPE, externtype)

WASMER_IMPORT_RESOURCE(functype)
WASMER_IMPORT_RESOURCE(globaltype)
WASMER_IMPORT_RESOURCE(memorytype)
WASMER_IMPORT_RESOURCE(tabletype)

extern zend_class_entry *wasm_exception_runtime_ce;

PHP_FUNCTION (wasm_externtype_kind) {
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    int kind = wasm_externtype_kind(WASMER_RES_P_INNER(externtype_val, externtype));

    RETURN_LONG(kind);
}

PHP_FUNCTION (wasm_externtype_as_functype) {
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasmer_res *functype = emalloc(sizeof(wasmer_res));
    functype->inner.functype = wasm_externtype_as_functype(WASMER_RES_P_INNER(externtype_val, externtype));
    functype->owned = false;

    WASMER_HANDLE_ERROR(wasm_exception_runtime_ce)

    zend_resource *functype_res = zend_register_resource(functype, le_wasm_functype);

    RETURN_RES(functype_res);
}

PHP_FUNCTION (wasm_externtype_as_globaltype) {
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasmer_res *globaltype = emalloc(sizeof(wasmer_res));
    globaltype->inner.globaltype = wasm_externtype_as_globaltype(WASMER_RES_P_INNER(externtype_val, externtype));
    globaltype->owned = false;

    WASMER_HANDLE_ERROR(wasm_exception_runtime_ce)

    zend_resource *globaltype_res = zend_register_resource(globaltype, le_wasm_globaltype);

    RETURN_RES(globaltype_res);
}

PHP_FUNCTION (wasm_externtype_as_memorytype) {
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasmer_res *memorytype = emalloc(sizeof(wasmer_res));
    memorytype->inner.memorytype = wasm_externtype_as_memorytype(WASMER_RES_P_INNER(externtype_val, externtype));
    memorytype->owned = false;

    WASMER_HANDLE_ERROR(wasm_exception_runtime_ce)

    zend_resource *memorytype_res = zend_register_resource(memorytype, le_wasm_memorytype);

    RETURN_RES(memorytype_res);
}

PHP_FUNCTION (wasm_externtype_as_tabletype) {
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasmer_res *tabletype = emalloc(sizeof(wasmer_res));
    tabletype->inner.tabletype = wasm_externtype_as_tabletype(WASMER_RES_P_INNER(externtype_val, externtype));
    tabletype->owned = false;

    WASMER_HANDLE_ERROR(wasm_exception_runtime_ce)

    zend_resource *tabletype_res = zend_register_resource(tabletype, le_wasm_tabletype);

    RETURN_RES(tabletype_res);
}
