#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_OWN(global)
WASMER_COPY(global)

WASMER_IMPORT_RESOURCE(store)
WASMER_IMPORT_RESOURCE(globaltype)
WASMER_IMPORT_RESOURCE(val)
WASMER_IMPORT_RESOURCE(extern)

PHP_FUNCTION (wasm_global_new) {
    zval *store_val;
    zval *globaltype_val;
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 3, 3)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_RESOURCE(globaltype_val)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)
    WASMER_FETCH_RESOURCE(globaltype)
    WASMER_FETCH_RESOURCE(val)

    wasmer_res *wasm_global = emalloc(sizeof(wasmer_res));
    wasm_global->inner.global = wasm_global_new(
            WASMER_RES_P_INNER(store_val, store),
            WASMER_RES_P_INNER(globaltype_val, globaltype),
            &WASMER_RES_P_INNER(val_val, val)
    );
    wasm_global->owned = true;

    zend_resource *val_res = zend_register_resource(wasm_global, le_wasm_global);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_global_type) {
    zval *global_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(global_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(global);

    wasmer_res *wasm_globaltype = emalloc(sizeof(wasmer_res));
    wasm_globaltype->inner.globaltype = wasm_global_type(WASMER_RES_P_INNER(global_val, global));
    wasm_globaltype->owned = true;

    zend_resource *val_res = zend_register_resource(wasm_globaltype, le_wasm_globaltype);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_global_get) {
    zval *global_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(global_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(global);

    wasm_val_t *val = emalloc(sizeof(wasm_val_t));
    wasm_global_get(WASMER_RES_P_INNER(global_val, global), val);
    wasmer_res *wasm_val = emalloc(sizeof(wasmer_res));
    wasm_val->inner.val = *val;
    // TODO(jubianchi): the returned val is owned but marking it as such ends up in a double-free
    wasm_val->owned = false;

    efree(val);

    zend_resource *val_res = zend_register_resource(wasm_val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_global_set) {
    zval *global_val;
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(global_val)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(global);
    WASMER_FETCH_RESOURCE(val);

    wasm_global_set(WASMER_RES_P_INNER(global_val, global), &WASMER_RES_P_INNER(val_val, val));

    WASMER_HANDLE_ERROR(zend_ce_exception)
}

PHP_FUNCTION (wasm_global_same) {
    zval *left_val;
    zval *right_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(left_val)
            Z_PARAM_RESOURCE(right_val)
    ZEND_PARSE_PARAMETERS_END();

    if (zend_fetch_resource_ex(left_val, le_wasm_global_name, le_wasm_global) == NULL) {
        RETURN_THROWS();
    }

    if (zend_fetch_resource_ex(right_val, le_wasm_global_name, le_wasm_global) == NULL) {
        RETURN_THROWS();
    }

    RETURN_BOOL(wasm_global_same(
            ((wasmer_res*)Z_RES_P(left_val)->ptr)->inner.global,
            ((wasmer_res*)Z_RES_P(right_val)->ptr)->inner.global
    ));
}

PHP_FUNCTION (wasm_global_as_extern) {
    zval *global_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(global_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(global)

    wasmer_res *wasm_extern = emalloc(sizeof(wasmer_res));
    wasm_extern->inner.xtern = wasm_global_as_extern(WASMER_RES_P_INNER(global_val, global));
    wasm_extern->owned = true;

    RETURN_RES(zend_register_resource(wasm_extern, le_wasm_extern));
}
