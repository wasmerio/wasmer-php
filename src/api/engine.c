#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "./macros.h"
#include "../wasmer.h"

WASMER_DECLARE_OWN(engine)

WASMER_IMPORT_RESOURCE(config)

extern zend_class_entry *wasm_exception_runtime_ce;

PHP_FUNCTION (wasm_engine_new) {
    ZEND_PARSE_PARAMETERS_NONE();

    wasmer_res *engine = emalloc(sizeof(wasmer_res));
    engine->inner.engine = wasm_engine_new();
    engine->owned = true;

    zend_resource *engine_res = zend_register_resource(engine, le_wasm_engine);

    RETURN_RES(engine_res);
}

PHP_FUNCTION (wasm_engine_new_with_config) {
    zval *config_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(config_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(config)

    wasmer_res *engine = emalloc(sizeof(wasmer_res));
    engine->inner.engine = wasm_engine_new_with_config(WASMER_RES_P_INNER(config_val, config));
    engine->owned = true;

    WASMER_HANDLE_ERROR_START
        efree(engine);
    WASMER_HANDLE_ERROR_END(wasm_exception_runtime_ce)

    zend_resource *engine_res;
    engine_res = zend_register_resource(engine, le_wasm_engine);

    RETURN_RES(engine_res);
}
