#include "php.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_IMPORT_RESOURCE(limits)

PHP_FUNCTION (wasm_limits_new) {
    zval *min_val;
    zval *max_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(min_val)
            Z_PARAM_LONG(max_val)
    ZEND_PARSE_PARAMETERS_END();

    wasm_limits_t limits = {.min = min_val, .max = max_val};
    wasmer_res *wasm_limits = emalloc(sizeof(wasmer_res));
    wasm_limits->inner.limits = limits;
    wasm_limits->owned = false;

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