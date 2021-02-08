#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_TYPE(ValType, VALTYPE, valtype)

PHP_FUNCTION (wasm_valtype_new) {
    zval *valkind_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_NUMBER(valkind_val)
    ZEND_PARSE_PARAMETERS_END();

    int valkind = zval_get_long(valkind_val);

    wasmer_res *valtype = emalloc(sizeof(wasmer_res));
    valtype->inner.valtype = wasm_valtype_new(valkind);
    valtype->owned = true;

    zend_resource *valtype_res = zend_register_resource(valtype, le_wasm_valtype);

    RETURN_RES(valtype_res);
}

PHP_FUNCTION (wasm_valtype_kind) {
    zval *valtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(valtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(valtype)

    int kind = wasm_valtype_kind(WASMER_RES_P_INNER(valtype_val, valtype));

    RETURN_LONG(kind);
}

PHP_FUNCTION (wasm_valtype_is_num) {
    zval *valtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(valtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(valtype)

    bool is_num = wasm_valtype_is_num(WASMER_RES_P_INNER(valtype_val, valtype));

    RETURN_BOOL(is_num);
}

PHP_FUNCTION (wasm_valtype_is_ref) {
    zval *valtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(valtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(valtype)

    bool is_ref = wasm_valtype_is_ref(WASMER_RES_P_INNER(valtype_val, valtype));

    RETURN_BOOL(is_ref);
}
