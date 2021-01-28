#include "php.h"

#include "wasm.h"

#include "../macros.h"

WASMER_IMPORT_RESOURCE(foreign)

PHP_FUNCTION (wasm_foreign_new) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

WASMER_DELETE_RESOURCE(foreign)

// TODO(jubianchi): Implement copy
