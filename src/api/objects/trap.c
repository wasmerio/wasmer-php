#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_OWN(trap)
WASMER_COPY(trap)

WASMER_IMPORT_RESOURCE(store)

// TODO(jubianchi): Handle wasmer errors
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
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_trap_trace) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}
