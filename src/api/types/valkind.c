#include "php.h"

#include "wasm.h"

PHP_FUNCTION (wasm_valkind_is_num) {
    zval *valkind_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_NUMBER(valkind_val)
    ZEND_PARSE_PARAMETERS_END();

    int valkind = zval_get_long(valkind_val);

    RETURN_BOOL(wasm_valkind_is_num(valkind));
}

PHP_FUNCTION (wasm_valkind_is_ref) {
    zval *valkind_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_NUMBER(valkind_val)
    ZEND_PARSE_PARAMETERS_END();

    int valkind = zval_get_long(valkind_val);

    RETURN_BOOL(wasm_valkind_is_ref(valkind));
}
