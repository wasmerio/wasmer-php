#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_OWN(val)

PHP_FUNCTION (wasm_val_value) {
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(val)

    const wasm_val_t val = WASMER_RES_P_INNER(val_val, val);

    switch (val.kind) {
        case WASM_I32:
            RETURN_LONG(val.of.i32);

        case WASM_I64:
            RETURN_LONG(val.of.i64);

        case WASM_F32:
            RETURN_DOUBLE(val.of.f32);

        case WASM_F64:
            RETURN_DOUBLE(val.of.f64);
    }

    RETURN_NULL();
}

PHP_FUNCTION (wasm_val_kind) {
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(val)

    RETURN_LONG(WASMER_RES_P_INNER(val_val, val).kind);
}

PHP_FUNCTION (wasm_val_copy) {
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(val)

    wasm_val_t val;
    wasm_val_copy(&val, &WASMER_RES_P_INNER(val_val, val));

    WASMER_HANDLE_ERROR

    zend_resource *val_res;
    val_res = zend_register_resource(&val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_val_i32) {
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_t val = {.kind = WASM_I32, .of = {.i32 = value}};

    wasmer_res *wasm_val = emalloc(sizeof(wasmer_res));
    wasm_val->inner.val = val;
    wasm_val->owned = false;

    zend_resource *val_res = zend_register_resource(wasm_val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_val_i64) {
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_t val = {.kind = WASM_I64, .of = {.i64 = value}};

    wasmer_res *wam_val = emalloc(sizeof(wasmer_res));
    wam_val->inner.val = val;
    wam_val->owned = false;

    zend_resource *val_res = zend_register_resource(wam_val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_val_f32) {
    double value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_DOUBLE(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_t val = {.kind = WASM_F32, .of = {.f32 = value}};

    wasmer_res *wam_val = emalloc(sizeof(wasmer_res));
    wam_val->inner.val = val;
    wam_val->owned = false;

    zend_resource *val_res = zend_register_resource(wam_val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_val_f64) {
    double value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_DOUBLE(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_t val = {.kind = WASM_F64, .of = {.f64 = value}};

    wasmer_res *wam_val = emalloc(sizeof(wasmer_res));
    wam_val->inner.val = val;
    wam_val->owned = false;

    zend_resource *val_res = zend_register_resource(wam_val, le_wasm_val);

    RETURN_RES(val_res);
}

PHP_METHOD (Wasm_Vec_Val, __construct) {
    zend_array *vals_ht;
    zend_long size;
    zend_bool is_null = 1;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 0, 1)
            Z_PARAM_OPTIONAL
            Z_PARAM_ARRAY_HT_OR_LONG_OR_NULL(vals_ht, size, is_null)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_vec_c *wasm_val_vec = WASMER_VAL_VEC_P(ZEND_THIS);
    wasm_val_vec_t vec;

    if (is_null) {
        wasm_val_vec_new_empty(&vec);\
    } else if(vals_ht) {
        int len = zend_hash_num_elements(vals_ht);
        wasm_val_vec_new_uninitialized(&vec, len);

        zval *tmp;
        zend_ulong index;

        ZEND_HASH_REVERSE_FOREACH_NUM_KEY_VAL(vals_ht, index, tmp) {
                wasmer_res *val_res = WASMER_RES_P(tmp);
                val_res->owned = false;

                vec.data[index] = WASMER_RES_INNER(val_res, val);
        } ZEND_HASH_FOREACH_END();
    } else {
        wasm_val_vec_new_uninitialized(&vec, size);
    }

    wasm_val_vec->vec = vec;
}

PHP_METHOD (Wasm_Vec_Val, count) {
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_val_vec_c *wasm_val_vec = WASMER_VAL_VEC_P(ZEND_THIS);

    RETURN_LONG(wasm_val_vec->vec.size);
}

PHP_METHOD (Wasm_Vec_Val, offsetExists) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_vec_c *wasm_val_vec = WASMER_VAL_VEC_P(ZEND_THIS);

    if(offset >= wasm_val_vec->vec.size) {
        RETURN_FALSE;
    }

    RETURN_BOOL(offset < wasm_val_vec->vec.size);
}

PHP_METHOD (Wasm_Vec_Val, offsetGet) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_val_vec_c *wasm_val_vec = WASMER_VAL_VEC_P(ZEND_THIS);

    if(offset >= wasm_val_vec->vec.size) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Val::offsetGet($offset) index out of bounds");
    }

    if(&wasm_val_vec->vec.data[offset] == NULL) {
        RETURN_NULL();
    }

    wasmer_res *val_res = emalloc(sizeof(wasmer_res));
    val_res->inner.val = wasm_val_vec->vec.data[offset];
    val_res->owned = false;

    RETURN_RES(zend_register_resource(val_res, le_wasm_val));
}

PHP_METHOD (Wasm_Vec_Val, offsetSet) {
    zend_long offset;
    zval *val_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_RESOURCE(val_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(val)

    wasm_val_vec_c *wasm_val_vec = WASMER_VAL_VEC_P(ZEND_THIS);

    if(offset >= wasm_val_vec->vec.size) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Val::offsetSet($offset) index out of bounds");
    }

    wasmer_res *val_res = WASMER_RES_P(val_val);
    val_res->owned = false;

    wasm_val_vec->vec.data[offset] = WASMER_RES_INNER(val_res, val);
}

PHP_METHOD (Wasm_Vec_Val, offsetUnset) {\
    zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Val::offsetUnset($offset) not available");\
}
