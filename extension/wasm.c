/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2018 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Ivan Enderlin                                                |
  +----------------------------------------------------------------------+
*/

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "php_wasm.h"
#include "wasmer.h"

/**
 * `wasm_read_bytes`.
 */

char* wasm_bytes_resource_name;
int wasm_bytes_resource_number;

uint8_t *wasm_bytes_from_resource(zend_resource *wasm_bytes_resource)
{
    return (uint8_t *) zend_fetch_resource(
        wasm_bytes_resource,
        wasm_bytes_resource_name,
        wasm_bytes_resource_number
    );
}

static void wasm_bytes_destructor(zend_resource *resource)
{
    uint8_t *wasm_bytes = wasm_bytes_from_resource(resource);
    free(wasm_bytes);
}

PHP_FUNCTION(wasm_read_bytes)
{
    char *file_path;
    size_t file_path_length;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "p", &file_path, &file_path_length) == FAILURE) {
        return;
    }

    // Read the Wasm file bytes.
    FILE *wasm_file = fopen(file_path, "r");
    fseek(wasm_file, 0, SEEK_END);

    size_t wasm_file_length = ftell(wasm_file);
    uint8_t *wasm_bytes = malloc(wasm_file_length);
    fseek(wasm_file, 0, SEEK_SET);

    fread(wasm_bytes, 1, wasm_file_length, wasm_file);

    fclose(wasm_file);

    zend_resource *resource = zend_register_resource((void *) wasm_bytes, wasm_bytes_resource_number);

    RETURN_RES(resource);
}
//
///**
// * `wasm_new_runtime`.
// */
//
//char* wasm_runtime_resource_name;
//int wasm_runtime_resource_number;
//
//Runtime *wasm_runtime_from_resource(zend_resource *wasm_runtime_resource)
//{
//    return (Runtime *) zend_fetch_resource(
//        wasm_runtime_resource,
//        wasm_runtime_resource_name,
//        wasm_runtime_resource_number
//    );
//}
//
//static void wasm_runtime_destructor(zend_resource *resource)
//{
//    Runtime *wasm_runtime = wasm_runtime_from_resource(resource);
//    drop_wasm_runtime(wasm_runtime);
//}
//
//PHP_FUNCTION(wasm_new_runtime)
//{
//    if (zend_parse_parameters_none() == FAILURE) {
//        return;
//    }
//
//    Runtime *wasm_runtime = wasm_new_runtime();
//    zend_resource *resource = zend_register_resource((void *) wasm_runtime, wasm_runtime_resource_number);
//
//    RETURN_RES(resource);
//}
//
//const Value* invoke_imported_function_from_host(
//    const /* zend_fcall_info */ void *function_implementation,
//    const /* zend_fcall_info_cache */ void *function_implementation_cache,
//    const FunctionInputs *function_inputs
//) {
//    zend_fcall_info *fci = (zend_fcall_info *) function_implementation;
//    zend_fcall_info_cache *fci_cache = (zend_fcall_info_cache *) function_implementation_cache;
//    zval output;
//
//    uintptr_t inputs_length = function_inputs->inputs_length;
//    zval *inputs = (zval *) malloc(inputs_length * sizeof(zval));
//
//    for (uintptr_t nth = 0; nth < inputs_length; ++nth) {
//        const Value value = function_inputs->inputs_buffer[nth];
//        Value_Tag tag = value.tag;
//
//        if (tag == I32) {
//            ZVAL_LONG(&inputs[nth], value.i32._0);
//        } else if (tag == I64) {
//            ZVAL_LONG(&inputs[nth], value.i64._0);
//        } else if (tag == F32) {
//            ZVAL_DOUBLE(&inputs[nth], value.f32._0);
//        } else if (tag == F64) {
//            ZVAL_DOUBLE(&inputs[nth], value.f64._0);
//        } else if (tag == None) {
//            free(inputs);
//
//            return NULL;
//        }
//    }
//
//    fci->retval = &output;
//    fci->param_count = inputs_length;
//    fci->params = inputs;
//    fci->no_separation = 0;
//
//    if (zend_call_function(fci, fci_cache) != SUCCESS) {
//        free(inputs);
//
//        return NULL;
//    }
//
//    free(inputs);
//
//    switch (Z_TYPE_P(&output)) {
//        case IS_LONG: {
//            I32_Body value = { output.value.lval };
//            Value* function_output = (Value*) malloc(sizeof(Value));
//            function_output->tag = I32;
//            function_output->i32 = value;
//
//            return function_output;
//        }
//
//        case IS_DOUBLE: {
//            F32_Body value = { output.value.dval };
//            Value* function_output = (Value*) malloc(sizeof(Value));
//            function_output->tag = F32;
//            function_output->f32 = value;
//
//            return function_output;
//        }
//
//        default:
//            return NULL;
//    }
//}
//
///**
// * `wasm_runtime_add_function`.
// */
//
//PHP_FUNCTION(wasm_runtime_add_function)
//{
//    zval *wasm_runtime_resource;
//    double index;
//    char *function_name;
//    size_t function_name_length;
//    HashTable *signature;
//    zend_fcall_info *function_implementation = (zend_fcall_info *) malloc(sizeof(zend_fcall_info));
//    zend_fcall_info_cache *function_implementation_cache =  (zend_fcall_info_cache *) malloc(sizeof(zend_fcall_info_cache));
//
//    if (
//        zend_parse_parameters(
//            ZEND_NUM_ARGS() TSRMLS_CC,
//            "rdshf",
//            &wasm_runtime_resource,
//            &index,
//            &function_name,
//            &function_name_length,
//            &signature,
//            function_implementation,
//            function_implementation_cache
//        ) == FAILURE
//    ) {
//        free(&function_implementation);
//        free(&function_implementation_cache);
//
//        return;
//    }
//
//    size_t signature_length = zend_hash_num_elements(signature);
//
//    // Must at least contain the output type.
//    if (signature_length == 0) {
//        free(&function_implementation);
//        free(&function_implementation_cache);
//
//        RETURN_FALSE
//    }
//
//    ValueType *ffi_signature = (ValueType*) malloc(signature_length * sizeof(ValueType));
//
//    {
//        zend_ulong key;
//        zval *value;
//
//        ZEND_HASH_FOREACH_NUM_KEY_VAL(signature, key, value)
//            ffi_signature[key] = value->value.lval;
//        ZEND_HASH_FOREACH_END();
//    }
//
//    const Value* (*callback)(const void*, const void*, const FunctionInputs*);
//    callback = &invoke_imported_function_from_host;
//
//    Runtime *wasm_runtime = wasm_runtime_from_resource(Z_RES_P(wasm_runtime_resource));
//    wasm_runtime_add_function(
//        wasm_runtime,
//        index,
//        function_name,
//        ffi_signature,
//        signature_length,
//        &callback,
//        function_implementation,
//        function_implementation_cache
//    );
//    
//    free(&ffi_signature);
//
//    RETURN_TRUE
//}
//
///**
// * `wasm_new_instance`.
// */
//
//char* wasm_instance_resource_name;
//int wasm_instance_resource_number;
//
//Instance *wasm_instance_from_resource(zend_resource *wasm_instance_resource)
//{
//    return (Instance *) zend_fetch_resource(
//        wasm_instance_resource,
//        wasm_instance_resource_name,
//        wasm_instance_resource_number
//    );
//}
//
//static void wasm_instance_destructor(zend_resource *resource)
//{
//    Instance *wasm_instance = wasm_instance_from_resource(resource);
//    drop_wasm_instance(wasm_instance);
//}
//
//PHP_FUNCTION(wasm_new_instance)
//{
//    char *file_path;
//    size_t file_path_length;
//    zval *wasm_binary_resource;
//    zval *wasm_runtime_resource;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "prr", &file_path, &file_path_length, &wasm_binary_resource, &wasm_runtime_resource) == FAILURE) {
//        return;
//    }
//
//    const Vec_u8 *wasm_binary = wasm_binary_from_resource(Z_RES_P(wasm_binary_resource));
//    const Runtime *wasm_runtime = wasm_runtime_from_resource(Z_RES_P(wasm_runtime_resource));
//    Instance *wasm_instance = wasm_new_instance(file_path, wasm_binary, wasm_runtime);
//
//    if (NULL == wasm_instance) {
//        RETURN_NULL();
//    }
//
//    zend_resource *resource = zend_register_resource((void *) wasm_instance, wasm_instance_resource_number);
//
//    RETURN_RES(resource);
//}
//
///**
// * `wasm_get_function_signature`.
// */
//
//PHP_FUNCTION(wasm_get_function_signature)
//{
//    zval *wasm_instance_resource;
//    char *function_name;
//    size_t function_name_length;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rs", &wasm_instance_resource, &function_name, &function_name_length) == FAILURE) {
//        return;
//    }
//
//    Instance *wasm_instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));
//    const Signature *wasm_function_signature = wasm_get_function_signature(wasm_instance, function_name);
//
//    if (NULL == wasm_function_signature) {
//        RETURN_NULL();
//    }
//
//    array_init_size(return_value, wasm_function_signature->inputs_length + 1 /* output */);
//
//    for (uintptr_t nth = 0; nth < wasm_function_signature->inputs_length; ++nth) {
//        add_next_index_long(return_value, wasm_function_signature->inputs_buffer[nth]);
//    }
//
//    add_next_index_long(return_value, *(wasm_function_signature->output));
//}
//
///**
// * `wasm_invoke_arguments_builder`.
// */
//
//char* wasm_invoke_arguments_builder_resource_name;
//int wasm_invoke_arguments_builder_resource_number;
//
//Vec_RuntimeValue *wasm_arguments_builder_from_resource(zend_resource *wasm_arguments_builder_resource)
//{
//    return (Vec_RuntimeValue *) zend_fetch_resource(
//        wasm_arguments_builder_resource,
//        wasm_invoke_arguments_builder_resource_name,
//        wasm_invoke_arguments_builder_resource_number
//    );
//}
//
//static void wasm_invoke_arguments_builder_destructor(zend_resource *resource)
//{
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(resource);
//    drop_wasm_arguments_builder(wasm_arguments_builder);
//}
//
//PHP_FUNCTION(wasm_invoke_arguments_builder)
//{
//    if (zend_parse_parameters_none() == FAILURE) {
//        return;
//    }
//
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_invoke_arguments_builder();
//    zend_resource *resource = zend_register_resource((void *) wasm_arguments_builder, wasm_invoke_arguments_builder_resource_number);
//
//    RETURN_RES(resource);
//}
//
///**
// * `wasm_invoke_arguments_builder_add_i32`.
// */
//
//PHP_FUNCTION(wasm_invoke_arguments_builder_add_i32)
//{
//    zval *wasm_arguments_builder_resource;
//    zend_long number;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rl", &wasm_arguments_builder_resource, &number) == FAILURE) {
//        return;
//    }
//
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(Z_RES_P(wasm_arguments_builder_resource));
//    wasm_invoke_arguments_builder_add_i32(wasm_arguments_builder, (int32_t) number);
//
//    RETURN_TRUE
//}
//
///**
// * `wasm_invoke_arguments_builder_add_i64`.
// */
//
//PHP_FUNCTION(wasm_invoke_arguments_builder_add_i64)
//{
//    zval *wasm_arguments_builder_resource;
//    zend_long number;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rl", &wasm_arguments_builder_resource, &number) == FAILURE) {
//        return;
//    }
//
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(Z_RES_P(wasm_arguments_builder_resource));
//    wasm_invoke_arguments_builder_add_i64(wasm_arguments_builder, (int64_t) number);
//
//    RETURN_TRUE
//}
//
///**
// * `wasm_invoke_arguments_builder_add_f32`.
// */
//
//PHP_FUNCTION(wasm_invoke_arguments_builder_add_f32)
//{
//    zval *wasm_arguments_builder_resource;
//    double number;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rd", &wasm_arguments_builder_resource, &number) == FAILURE) {
//        return;
//    }
//
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(Z_RES_P(wasm_arguments_builder_resource));
//    wasm_invoke_arguments_builder_add_f32(wasm_arguments_builder, (float) number);
//
//    RETURN_TRUE
//}
//
///**
// * `wasm_invoke_arguments_builder_add_f64`.
// */
//
//PHP_FUNCTION(wasm_invoke_arguments_builder_add_f64)
//{
//    zval *wasm_arguments_builder_resource;
//    double number;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rd", &wasm_arguments_builder_resource, &number) == FAILURE) {
//        return;
//    }
//
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(Z_RES_P(wasm_arguments_builder_resource));
//    wasm_invoke_arguments_builder_add_f64(wasm_arguments_builder, number);
//
//    RETURN_TRUE
//}
//
///**
// * `wasm_invoke_function`.
// */
//
//PHP_FUNCTION(wasm_invoke_function)
//{
//    zval *wasm_instance_resource;
//    char *function_name;
//    size_t function_name_length;
//    zval *wasm_arguments_builder_resource;
//    zval *wasm_runtime_resource;
//
//    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rsrr", &wasm_instance_resource, &function_name, &function_name_length, &wasm_arguments_builder_resource, &wasm_runtime_resource) == FAILURE) {
//        return;
//    }
//
//    Instance *wasm_instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));
//    Vec_RuntimeValue *wasm_arguments_builder = wasm_arguments_builder_from_resource(Z_RES_P(wasm_arguments_builder_resource));
//    Runtime *wasm_runtime = wasm_runtime_from_resource(Z_RES_P(wasm_runtime_resource));
//
//    const Value *value = wasm_invoke_function(wasm_instance, function_name, wasm_arguments_builder, wasm_runtime);
//
//    if (value->tag == I32) {
//        RETURN_LONG(value->i32._0);
//    } else if (value->tag == I64) {
//        RETURN_LONG(value->i64._0);
//    } else if (value->tag == F32) {
//        RETURN_DOUBLE(value->f32._0);
//    } else if (value->tag == F64) {
//        RETURN_DOUBLE(value->f64._0);
//    } else if (value->tag == None) {
//        RETURN_NULL();
//    } else {
//        RETURN_FALSE
//    }
//}

PHP_RINIT_FUNCTION(wasm)
{
#if defined(ZTS) && defined(COMPILE_DL_WASM)
    ZEND_TSRMLS_CACHE_UPDATE();
#endif

    return SUCCESS;
}

PHP_MINIT_FUNCTION(wasm)
{
    REGISTER_LONG_CONSTANT("WASM_SIGNATURE_TYPE_I32", WASM_I32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_SIGNATURE_TYPE_I64", WASM_I64, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_SIGNATURE_TYPE_F32", WASM_F32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_SIGNATURE_TYPE_F64", WASM_F64, CONST_CS | CONST_PERSISTENT);
//
//    wasm_binary_resource_name = "wasm_binary";
//    wasm_binary_resource_number = zend_register_list_destructors_ex(
//        wasm_binary_destructor,
//        NULL,
//        wasm_binary_resource_name,
//        module_number
//    );
//
//    wasm_runtime_resource_name = "wasm_runtime";
//    wasm_runtime_resource_number = zend_register_list_destructors_ex(
//        wasm_runtime_destructor,
//        NULL,
//        wasm_runtime_resource_name,
//        module_number
//    );
//
//    wasm_instance_resource_name = "wasm_instance";
//    wasm_instance_resource_number = zend_register_list_destructors_ex(
//        wasm_instance_destructor,
//        NULL,
//        wasm_instance_resource_name,
//        module_number
//    );
//
//    wasm_invoke_arguments_builder_resource_name = "wasm_invoke_arguments_builder";
//    wasm_invoke_arguments_builder_resource_number = zend_register_list_destructors_ex(
//        wasm_invoke_arguments_builder_destructor,
//        NULL,
//        wasm_invoke_arguments_builder_resource_name,
//        module_number
//    );

    return SUCCESS;
}

PHP_MINFO_FUNCTION(wasm)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "wasm support", "enabled");
    php_info_print_table_end();
}

ZEND_BEGIN_ARG_INFO(arginfo_wasm_read_bytes, 0)
    ZEND_ARG_INFO(0, file_path)
ZEND_END_ARG_INFO()

//ZEND_BEGIN_ARG_INFO(arginfo_wasm_new_runtime, 0)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_runtime_add_function, 0)
//    ZEND_ARG_INFO(1, wasm_runtime)
//    ZEND_ARG_INFO(0, index)
//    ZEND_ARG_INFO(0, function_name)
//    ZEND_ARG_INFO(0, function_signature)
//    ZEND_ARG_INFO(0, function_implementation)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_new_instance, 0)
//    ZEND_ARG_INFO(0, file_path)
//    ZEND_ARG_INFO(1, wasm_binary)
//    ZEND_ARG_INFO(1, wasm_runtime)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_get_function_signature, 0)
//    ZEND_ARG_INFO(1, wasm_instance)
//    ZEND_ARG_INFO(0, function_name)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_arguments_builder, 0)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_arguments_builder_add_i32, 0)
//    ZEND_ARG_INFO(1, wasm_invoke_arguments_builder)
//    ZEND_ARG_INFO(0, argument_value)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_arguments_builder_add_i64, 0)
//    ZEND_ARG_INFO(1, wasm_invoke_arguments_builder)
//    ZEND_ARG_INFO(0, argument_value)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_arguments_builder_add_f32, 0)
//    ZEND_ARG_INFO(1, wasm_invoke_arguments_builder)
//    ZEND_ARG_INFO(0, argument_value)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_arguments_builder_add_f64, 0)
//    ZEND_ARG_INFO(1, wasm_invoke_arguments_builder)
//    ZEND_ARG_INFO(0, argument_value)
//ZEND_END_ARG_INFO()
//
//ZEND_BEGIN_ARG_INFO(arginfo_wasm_invoke_function, 0)
//    ZEND_ARG_INFO(1, wasm_instance)
//    ZEND_ARG_INFO(0, function_name)
//    ZEND_ARG_INFO(1, wasm_invoke_arguments_builder)
//    ZEND_ARG_INFO(1, wasm_runtime)
//ZEND_END_ARG_INFO()

static const zend_function_entry wasm_functions[] = {
    PHP_FE(wasm_read_bytes,						arginfo_wasm_read_bytes)
    //PHP_FE(wasm_new_runtime,						arginfo_wasm_new_runtime)
    //PHP_FE(wasm_runtime_add_function,				arginfo_wasm_runtime_add_function)
    //PHP_FE(wasm_new_instance,						arginfo_wasm_new_instance)
    //PHP_FE(wasm_get_function_signature,				arginfo_wasm_get_function_signature)
    //PHP_FE(wasm_invoke_arguments_builder,			arginfo_wasm_invoke_arguments_builder)
    //PHP_FE(wasm_invoke_arguments_builder_add_i32,	arginfo_wasm_invoke_arguments_builder_add_i32)
    //PHP_FE(wasm_invoke_arguments_builder_add_i64,	arginfo_wasm_invoke_arguments_builder_add_i64)
    //PHP_FE(wasm_invoke_arguments_builder_add_f32,	arginfo_wasm_invoke_arguments_builder_add_f32)
    //PHP_FE(wasm_invoke_arguments_builder_add_f64,	arginfo_wasm_invoke_arguments_builder_add_f64)
    //PHP_FE(wasm_invoke_function,					arginfo_wasm_invoke_function)
    PHP_FE_END
};

zend_module_entry wasm_module_entry = {
    STANDARD_MODULE_HEADER,
    "wasm",					/* Extension name */
    wasm_functions,			/* zend_function_entry */
    PHP_MINIT(wasm),		/* PHP_MINIT - Module initialization */
    NULL,					/* PHP_MSHUTDOWN - Module shutdown */
    PHP_RINIT(wasm),		/* PHP_RINIT - Request initialization */
    NULL,					/* PHP_RSHUTDOWN - Request shutdown */
    PHP_MINFO(wasm),		/* PHP_MINFO - Module info */
    PHP_WASM_VERSION,		/* Version */
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_WASM
# ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
# endif
    ZEND_GET_MODULE(wasm)
#endif

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */
