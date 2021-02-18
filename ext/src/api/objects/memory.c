#include "php.h"

#include "wasm.h"

#include "../macros.h"

WASMER_IMPORT_RESOURCE(memory)

// TODO(jubianchi): Handle wasmer errors
PHP_FUNCTION (wasm_memory_new) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

WASMER_DELETE_RESOURCE(memory)

PHP_FUNCTION (wasm_memory_type) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_memory_data) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_memory_data_size) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_memory_size) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_memory_grow) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

// TODO(jubianchi): Implement copy

PHP_FUNCTION (wasm_memory_as_extern) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}
