#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_TYPE(GlobalType, GLOBALTYPE, globaltype)

WASMER_IMPORT_RESOURCE(externtype)
WASMER_IMPORT_RESOURCE(valtype)

PHP_FUNCTION (wasm_globaltype_new) {
    zval *valtype_val;
    zval *mutability_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(valtype_val)
            Z_PARAM_NUMBER(mutability_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(valtype)

    int mutability = zval_get_long(mutability_val);
    wasmer_res *valtype_res = WASMER_RES_P(valtype_val);
    valtype_res->owned = false;

    wasmer_res *globaltype = emalloc(sizeof(wasmer_res));
    globaltype->inner.globaltype = wasm_globaltype_new(WASMER_RES_INNER(valtype_res, valtype), mutability);
    globaltype->owned = true;

    zend_resource *globaltype_res = zend_register_resource(globaltype, le_wasm_globaltype);

    RETURN_RES(globaltype_res);
}

PHP_FUNCTION (wasm_globaltype_content) {
    zval *globaltype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(globaltype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(globaltype)

    wasmer_res *valtype = emalloc(sizeof(wasmer_res));
    valtype->inner.valtype = wasm_globaltype_content(WASMER_RES_P_INNER(globaltype_val, globaltype));
    valtype->owned = false;

    zend_resource *valtype_res = zend_register_resource(valtype, le_wasm_valtype);

    RETURN_RES(valtype_res);
}

PHP_FUNCTION (wasm_globaltype_mutability) {
    zval *globaltype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(globaltype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(globaltype)

    int mutability = wasm_globaltype_mutability(WASMER_RES_P_INNER(globaltype_val, globaltype));

    RETURN_LONG(mutability);
}

PHP_FUNCTION (wasm_globaltype_as_externtype) {
    zval *globaltype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(globaltype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(globaltype)

    wasmer_res *globaltype_res = WASMER_RES_P(globaltype_val);
    globaltype_res->owned = false;

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_globaltype_as_externtype(WASMER_RES_INNER(globaltype_res, globaltype));
    externtype->owned = false;

    zend_resource *externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
