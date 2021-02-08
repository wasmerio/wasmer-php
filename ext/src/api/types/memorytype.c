#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_TYPE(MemoryType, MEMORYTYPE, memorytype)

WASMER_IMPORT_RESOURCE(externtype)
WASMER_IMPORT_RESOURCE(limits)
WASMER_IMPORT_RESOURCE(valtype)

PHP_FUNCTION (wasm_memorytype_new) {
    zval *limits_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(limits_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(limits)

    wasmer_res *memorytype = emalloc(sizeof(wasmer_res));
    memorytype->inner.memorytype = wasm_memorytype_new(Z_RES_P(limits_val)->ptr);
    memorytype->owned = true;

    zend_resource *memorytype_res = zend_register_resource(memorytype, le_wasm_memorytype);

    RETURN_RES(memorytype_res);
}

PHP_FUNCTION (wasm_memorytype_limits) {
    zval *memorytype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memorytype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memorytype)

    wasmer_res *wasm_limits = emalloc(sizeof(wasmer_res));
    wasm_limits->inner.limits = *wasm_memorytype_limits(WASMER_RES_P_INNER(memorytype_val, memorytype));

    zend_resource *limits_res = zend_register_resource(wasm_limits, le_wasm_limits);

    RETURN_RES(limits_res);
}

PHP_FUNCTION (wasm_memorytype_as_externtype) {
    zval *memorytype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memorytype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memorytype)

    wasmer_res *memorytype_res = WASMER_RES_P(memorytype_val);
    memorytype_res->owned = false;

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_memorytype_as_externtype(WASMER_RES_INNER(memorytype_res, memorytype));
    externtype->owned = false;

    zend_resource *externtype_res;
    externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
