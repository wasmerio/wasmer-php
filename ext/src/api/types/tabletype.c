#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_TYPE(TableType, TABLETYPE, tabletype)

WASMER_IMPORT_RESOURCE(externtype)
WASMER_IMPORT_RESOURCE(limits)
WASMER_IMPORT_RESOURCE(valtype)

PHP_FUNCTION (wasm_tabletype_new) {
    zval *valtype_val;
    zval *limits_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(valtype_val)
            Z_PARAM_RESOURCE(limits_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(valtype)
    WASMER_FETCH_RESOURCE(limits)

    wasmer_res *valtype_res = WASMER_RES_P(valtype_val);
    valtype_res->owned = false;

    wasmer_res *limits_res = WASMER_RES_P(limits_val);
    limits_res->owned = false;

    wasmer_res *tabletype = emalloc(sizeof(wasmer_res));
    tabletype->inner.tabletype = wasm_tabletype_new(WASMER_RES_INNER(valtype_res, valtype), &WASMER_RES_INNER(limits_res, limits));
    tabletype->owned = true;

    zend_resource *tabletype_res = zend_register_resource(tabletype, le_wasm_tabletype);

    RETURN_RES(tabletype_res);
}

PHP_FUNCTION (wasm_tabletype_element) {
    zval *tabletype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(tabletype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(tabletype)

    wasmer_res *valtype = emalloc(sizeof(wasmer_res));
    valtype->inner.valtype = wasm_tabletype_element(WASMER_RES_P_INNER(tabletype_val, tabletype));
    valtype->owned = false;

    zend_resource *valtype_res = zend_register_resource((void *) valtype, le_wasm_valtype);

    RETURN_RES(valtype_res);
}

PHP_FUNCTION (wasm_tabletype_limits) {
    zval *tabletype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(tabletype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(tabletype)

    wasmer_res *wasm_limits = ecalloc(1, sizeof(wasmer_res));
    wasm_limits->inner.limits = *wasm_tabletype_limits(WASMER_RES_P_INNER(tabletype_val, tabletype));
    wasm_limits->owned = false;

    zend_resource *limits_res = zend_register_resource(wasm_limits, le_wasm_limits);

    RETURN_RES(limits_res);
}

PHP_FUNCTION (wasm_tabletype_as_externtype) {
    zval *tabletype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(tabletype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(tabletype)

    wasmer_res *tabletype_res = WASMER_RES_P(tabletype_val);
    tabletype_res->owned = false;

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_tabletype_as_externtype(WASMER_RES_INNER(tabletype_res, tabletype));
    externtype->owned = false;

    zend_resource *externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
