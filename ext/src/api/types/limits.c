#include "php.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_IMPORT_RESOURCE(limits)

PHP_FUNCTION (wasm_limits_new) {
    zval *min_val;
    zval *max_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_NUMBER(min_val)
            Z_PARAM_NUMBER(max_val)
    ZEND_PARSE_PARAMETERS_END();

    wasm_limits_t limits = {.min = zval_get_long(min_val), .max = zval_get_long(max_val)};
    wasmer_res *wasm_limits = emalloc(sizeof(wasmer_res));
    wasm_limits->inner.limits = limits;

    zend_resource *limits_res = zend_register_resource(wasm_limits, le_wasm_limits);

    RETURN_RES(limits_res);
}

PHP_FUNCTION (wasm_limits_min) {
    zval *limits_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(limits_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(limits)

    wasm_limits_t limits = WASMER_RES_P_INNER(limits_val, limits);

    RETURN_LONG(limits.min);
}

PHP_FUNCTION (wasm_limits_max) {
    zval *limits_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(limits_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(limits)

    wasm_limits_t limits = WASMER_RES_P_INNER(limits_val, limits);

    RETURN_LONG(limits.max);
}