#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_OWN(extern)

WASMER_IMPORT_RESOURCE(func)
WASMER_IMPORT_RESOURCE(global)
WASMER_IMPORT_RESOURCE(table)
WASMER_IMPORT_RESOURCE(memory)

PHP_FUNCTION (wasm_extern_kind) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_extern_type) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_extern_as_func) {
    zval *extern_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(extern_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(extern)

    wasmer_res *func = emalloc(sizeof(wasmer_res));
    func->inner.func = wasm_extern_as_func(WASMER_RES_P_INNER(extern_val, xtern));
    func->owned = true;

    RETURN_RES(zend_register_resource(func, le_wasm_func));
}

PHP_FUNCTION (wasm_extern_as_global) {
    zval *extern_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(extern_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(extern)

    wasmer_res *global = emalloc(sizeof(wasmer_res));
    global->inner.global = wasm_extern_as_global(WASMER_RES_P_INNER(extern_val, xtern));
    global->owned = false;

    RETURN_RES(zend_register_resource(global, le_wasm_global));
}

PHP_FUNCTION (wasm_extern_as_table) {
    zval *extern_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(extern_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(extern)

    wasmer_res *table = emalloc(sizeof(wasmer_res));
    table->inner.table = wasm_extern_as_table(WASMER_RES_P_INNER(extern_val, xtern));
    table->owned = false;

    RETURN_RES(zend_register_resource(table, le_wasm_table));
}

PHP_FUNCTION (wasm_extern_as_memory) {
    zval *extern_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(extern_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(extern)

    wasmer_res *memory = emalloc(sizeof(wasmer_res));
    memory->inner.memory = wasm_extern_as_memory(WASMER_RES_P_INNER(extern_val, xtern));
    memory->owned = false;

    RETURN_RES(zend_register_resource(memory, le_wasm_memory));
}

PHP_METHOD (Wasm_Vec_Extern, __construct) {
    zend_array *externs_ht;
    zend_long size;
    zend_bool is_null = 1;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 0, 1)
            Z_PARAM_OPTIONAL
            Z_PARAM_ARRAY_HT_OR_LONG_OR_NULL(externs_ht, size, is_null)
    ZEND_PARSE_PARAMETERS_END();

    wasm_extern_vec_c *wasm_extern_vec = WASMER_EXTERN_VEC_P(ZEND_THIS);
    wasm_extern_vec_t vec;

    if (is_null) {
        wasm_extern_vec_new_empty(&vec);\
    } else if(externs_ht) {
        int len = zend_hash_num_elements(externs_ht);
        wasm_extern_vec_new_uninitialized(&vec, len);

        zval *tmp;
        zend_ulong index;

        ZEND_HASH_REVERSE_FOREACH_NUM_KEY_VAL(externs_ht, index, tmp) {
                    wasmer_res *extern_res = WASMER_RES_P(tmp);
                    extern_res->owned = false;

                    vec.data[index] = WASMER_RES_INNER(extern_res, xtern);
        } ZEND_HASH_FOREACH_END();
    } else {
        wasm_extern_vec_new_uninitialized(&vec, size);
    }

    wasm_extern_vec->vec = vec;
}

PHP_METHOD (Wasm_Vec_Extern, count) {
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_extern_vec_c *wasm_extern_vec = WASMER_EXTERN_VEC_P(ZEND_THIS);

    RETURN_LONG(wasm_extern_vec->vec.size);
}

PHP_METHOD (Wasm_Vec_Extern, offsetExists) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_extern_vec_c *wasm_extern_vec = WASMER_EXTERN_VEC_P(ZEND_THIS);

    if(offset >= wasm_extern_vec->vec.size) {
        RETURN_FALSE;
    }

    RETURN_BOOL(offset < wasm_extern_vec->vec.size);
}

PHP_METHOD (Wasm_Vec_Extern, offsetGet) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_extern_vec_c *wasm_extern_vec = WASMER_EXTERN_VEC_P(ZEND_THIS);

    if(offset >= wasm_extern_vec->vec.size) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Extern::offsetGet($offset) index out of bounds");
    }

    if(wasm_extern_vec->vec.data[offset] == NULL) {
        RETURN_NULL();
    }

    wasmer_res *xtern = emalloc(sizeof(wasmer_res));
    xtern->inner.xtern = wasm_extern_vec->vec.data[offset];
    xtern->owned = false;

    RETURN_RES(zend_register_resource(xtern, le_wasm_extern));
}

PHP_METHOD (Wasm_Vec_Extern, offsetSet) {
    zend_long offset;
    zval *extern_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_RESOURCE(extern_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(extern)

    wasm_extern_vec_c *wasm_extern_vec = WASMER_EXTERN_VEC_P(ZEND_THIS);

    if(offset >= wasm_extern_vec->vec.size) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Extern::offsetSet($offset) index out of bounds");
    }

    wasmer_res *extern_res = WASMER_RES_P(extern_val);
    extern_res->owned = false;

    wasm_extern_vec->vec.data[offset] = WASMER_RES_INNER(extern_res, xtern);
}

PHP_METHOD (Wasm_Vec_Extern, offsetUnset) {\
    zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\Extern::offsetUnset($offset) not available");\
}
