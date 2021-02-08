#include "php.h"

#include "wasmer_wasm.h"

#include "./macros.h"
#include "../wasmer.h"

WASMER_DECLARE_OWN(config)

PHP_FUNCTION (wasm_config_new) {
    ZEND_PARSE_PARAMETERS_NONE();

    wasmer_res *config = emalloc(sizeof(wasmer_res));
    config->inner.config = wasm_config_new();
    config->owned = true;

    zend_resource *config_res = zend_register_resource(config, le_wasm_config);

    RETURN_RES(config_res);
}

PHP_FUNCTION (wasm_config_set_compiler) {
    zval *config_val;
    zval *compiler_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(config_val)
            Z_PARAM_NUMBER(compiler_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(config)

    int compiler = zval_get_long(compiler_val);
    wasm_config_set_compiler(WASMER_RES_P_INNER(config_val, config), compiler);

    RETURN_TRUE;
}

PHP_FUNCTION (wasm_config_set_engine) {
    zval *config_val;
    zval *engine_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(config_val)
            Z_PARAM_NUMBER(engine_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(config)

    int engine = zval_get_long(engine_val);
    wasm_config_set_engine(WASMER_RES_P_INNER(config_val, config), engine);

    RETURN_TRUE;
}
