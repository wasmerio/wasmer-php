#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "macros.h"

extern zend_class_entry *wasm_exception_runtime_ce;

PHP_FUNCTION (wat2wasm) {
    char *wat;
    size_t wat_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_STRING(wat, wat_len)
    ZEND_PARSE_PARAMETERS_END();

    wasm_byte_vec_t *wat_vec = emalloc(sizeof(wasm_byte_vec_t));
    wat_vec->size = wat_len;
    wat_vec->data = wat;

    wasm_byte_vec_t *wasm_vec = emalloc(sizeof(wasm_byte_vec_t));;
    wat2wasm(wat_vec, wasm_vec);

    WASMER_HANDLE_ERROR_START
            efree(wat_vec);
            efree(wasm_vec);
    WASMER_HANDLE_ERROR_END(wasm_exception_runtime_ce)

    char *wasm = wasm_vec->data;
    int length = wasm_vec->size;

    efree(wat_vec);
    efree(wasm_vec);

    RETURN_STRINGL(wasm, length);
}
