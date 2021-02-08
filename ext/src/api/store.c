#include "php.h"

#include "wasm.h"

#include "./macros.h"
#include "../wasmer.h"

WASMER_DECLARE_OWN(store)

WASMER_IMPORT_RESOURCE(engine)

PHP_FUNCTION (wasm_store_new) {
    zval *engine_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(engine_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(engine)

    wasmer_res *store = emalloc(sizeof(wasmer_res));
    store->inner.store = wasm_store_new(WASMER_RES_P_INNER(engine_val, engine));
    store->owned = true;

    zend_resource *store_res;
    store_res = zend_register_resource(store, le_wasm_store);

    RETURN_RES(store_res);
}
