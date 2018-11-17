#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include "headers/php-ext-wasm.h"

int main(int argc, char **argv) {
    const Vec_u8 *wasm_binary = wasm_read_binary("./tests/toy.wasm");

    WASMInstance *wasm_instance = wasm_new_instance("./tests/toy.wasm", wasm_binary);

    if (NULL == wasm_instance) {
        printf("Cannot instanciate the WASM binary.");

        return 1;
    }

    Vec_RuntimeValue *arguments = wasm_invoke_arguments_builder();
    wasm_invoke_arguments_builder_add_i32(arguments, 1);
    wasm_invoke_arguments_builder_add_i32(arguments, 2);

    wasm_invoke_function(wasm_instance, "sum", arguments);
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
