#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_OWN(trap)
WASMER_COPY(trap)

WASMER_IMPORT_RESOURCE(store)
WASMER_IMPORT_RESOURCE(frame)

extern zend_class_entry *wasm_vec_frame_ce;

PHP_FUNCTION (wasm_trap_new) {
    zval *store_val;
    char *name;
    size_t name_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_STRING(name, name_len)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store);

    wasm_byte_vec_t *name_vec = malloc(sizeof(wasm_byte_vec_t));
    name_vec->size = name_len;
    name_vec->data = name;

    wasmer_res *trap = emalloc(sizeof(wasmer_res));
    trap->inner.trap = wasm_trap_new(WASMER_RES_P_INNER(store_val, store), name_vec);
    trap->owned = true;

    WASMER_HANDLE_ERROR_START
        efree(trap);
    WASMER_HANDLE_ERROR_END

    zend_resource *trap_res;
    trap_res = zend_register_resource(trap, le_wasm_trap);

    RETURN_RES(trap_res);
}

PHP_FUNCTION (wasm_trap_message) {
    zval *trap_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(trap_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(trap);

    wasm_byte_vec_t *name_vec = emalloc(sizeof(wasm_byte_vec_t));
    wasm_trap_message(WASMER_RES_P_INNER(trap_val, trap), name_vec);

    char *name = name_vec->data;
    int length = ((int) name_vec->size) - 1;

    efree(name_vec);

    RETURN_STRINGL(name, length);
}

PHP_FUNCTION (wasm_trap_origin) {
    zval *trap_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(trap_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(trap)

    wasm_frame_t *wasm_frame = wasm_trap_origin(WASMER_RES_P_INNER(trap_val, trap));

    if (wasm_frame) {
        wasmer_res *frame = emalloc(sizeof(wasmer_res));
        frame->inner.frame = wasm_trap_origin(WASMER_RES_P_INNER(trap_val, trap));
        frame->owned = true;

        RETURN_RES(zend_register_resource(frame, le_wasm_frame));
    }

    RETURN_NULL();
}

PHP_FUNCTION (wasm_trap_trace) {
    zval *trap_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(trap_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(trap)

    wasm_frame_vec_t *frames = emalloc(sizeof(wasm_exporttype_vec_t));
    wasm_trap_trace(WASMER_RES_P_INNER(trap_val, trap), frames);

    zval obj;
    object_init_ex(&obj, wasm_vec_frame_ce);
    wasm_frame_vec_c *ce = WASMER_FRAME_VEC_P(&obj);
    ce->vec.inner.frame = frames;
    ce->vec.owned = true;

    RETURN_OBJ(Z_OBJ(obj));
}
