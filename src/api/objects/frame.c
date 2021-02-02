#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_TYPE(Frame, FRAME, frame)

WASMER_IMPORT_RESOURCE(instance)

PHP_FUNCTION (wasm_frame_instance) {
    zval *frame_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(frame_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(frame)

    wasmer_res *func = emalloc(sizeof(wasmer_res));
    func->inner.instance = wasm_frame_instance(WASMER_RES_P_INNER(frame_val, frame));
    func->owned = false;

    RETURN_RES(zend_register_resource(func, le_wasm_instance));
}

PHP_FUNCTION (wasm_frame_func_index) {
    zval *frame_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(frame_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(frame)

    RETURN_LONG(wasm_frame_func_index(WASMER_RES_P_INNER(frame_val, frame)));
}

PHP_FUNCTION (wasm_frame_func_offset) {
    zval *frame_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(frame_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(frame)

    RETURN_LONG(wasm_frame_func_offset(WASMER_RES_P_INNER(frame_val, frame)));
}

PHP_FUNCTION (wasm_frame_module_offset) {
    zval *frame_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(frame_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(frame)

    RETURN_LONG(wasm_frame_module_offset(WASMER_RES_P_INNER(frame_val, frame)));
}
