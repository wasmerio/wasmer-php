#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_TYPE(FuncType, FUNCTYPE, functype)

WASMER_IMPORT_RESOURCE(externtype)

extern zend_class_entry *wasm_vec_valtype_ce;

PHP_FUNCTION (wasm_functype_new) {
    zval *params_val;
    zval *results_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_OBJECT(params_val)
            Z_PARAM_OBJECT(results_val)
    ZEND_PARSE_PARAMETERS_END();

    wasm_valtype_vec_c *params = WASMER_VALTYPE_VEC_P(params_val);
    params->vec.owned = false;
    wasm_valtype_vec_c *results = WASMER_VALTYPE_VEC_P(results_val);
    params->vec.owned = false;

    wasmer_res *functype = emalloc(sizeof(wasmer_res));
    functype->inner.functype = wasm_functype_new(params->vec.inner.valtype, results->vec.inner.valtype);
    functype->owned = true;

    zend_resource *functype_res = zend_register_resource(functype, le_wasm_functype);

    RETURN_RES(functype_res);
}

PHP_FUNCTION (wasm_functype_params) {
    zval *functype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(functype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(functype)

    const wasm_valtype_vec_t *valtypes = wasm_functype_params(WASMER_RES_P_INNER(functype_val, functype));

    zval obj;
    object_init_ex(&obj, wasm_vec_valtype_ce);
    wasm_valtype_vec_c *ce = WASMER_VALTYPE_VEC_P(&obj);
    ce->vec.inner.valtype = valtypes;
    ce->vec.owned = false;

    RETURN_OBJ(Z_OBJ(obj));
}

PHP_FUNCTION (wasm_functype_results) {
    zval *functype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(functype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(functype)

    const wasm_valtype_vec_t *valtypes = wasm_functype_results(WASMER_RES_P_INNER(functype_val, functype));

    zval obj;
    object_init_ex(&obj, wasm_vec_valtype_ce);
    wasm_valtype_vec_c *ce = WASMER_VALTYPE_VEC_P(&obj);
    ce->vec.inner.valtype = valtypes;
    ce->vec.owned = false;

    RETURN_OBJ(Z_OBJ(obj));
}

PHP_FUNCTION (wasm_functype_as_externtype) {
    zval *functype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(functype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(functype)

    wasmer_res *functype_res = WASMER_RES_P(functype_val);
    functype_res->owned = false;

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_functype_as_externtype(WASMER_RES_INNER(functype_res, functype));
    externtype->owned = false;

    zend_resource *externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
