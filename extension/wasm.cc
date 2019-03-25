/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2019 The PHP Group                                |
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
#include "zend_exceptions.h"
#include "php_wasm.h"
#include "wasmer.hh"

// Constant to represent a not nullable (return) type.
#define NOT_NULLABLE 0

// Constant to represent a nullable (return) type.
#define NULLABLE 1

// Syntactic sugar to represent the arity of a function in
// `ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX`.
#define ARITY(n) n

/**
 * Utils.
 */
wasmer_value_tag from_zend_long_to_wasmer_value_tag(zend_long x)
{
    if (x > 0) {
        return (wasmer_value_tag) (uint32_t) x;
    } else {
        return wasmer_value_tag::WASM_I32;
    }
}

/**
 * Information for the `wasm_bytes` resource.
 */
const char* wasm_bytes_resource_name;
int wasm_bytes_resource_number;

// Represents a `wasmer_byte_array` that is filled lazily. The bytes
// are read from `file_path`.
typedef struct wasm_lazy_byte_array_t wasm_lazy_byte_array;
struct wasm_lazy_byte_array_t {
private:
    char* file_path;
    wasmer_byte_array *byte_array;

public:
    wasm_lazy_byte_array_t(char* file_path) :
        file_path(file_path),
        byte_array(NULL)
    {}

    ~wasm_lazy_byte_array_t()
    {
        efree((uint8_t *) byte_array->bytes);
    }

    wasmer_byte_array *get_bytes()
    {
        if (byte_array == NULL) {
            // Open the file.
            FILE *wasm_file = fopen(file_path, "r");

            if (wasm_file == NULL) {
                return NULL;
            }

            // Read the file content.
            fseek(wasm_file, 0, SEEK_END);

            size_t wasm_file_length = ftell(wasm_file);
            const uint8_t *wasm_bytes = (const uint8_t *) emalloc(wasm_file_length);
            fseek(wasm_file, 0, SEEK_SET);

            fread((uint8_t *) wasm_bytes, 1, wasm_file_length, wasm_file);

            // Close the file.
            fclose(wasm_file);

            // Store the bytes of the Wasm file into a `wasmer_byte_array` structure.
            byte_array = (wasmer_byte_array *) emalloc(sizeof(wasmer_byte_array));
            byte_array->bytes = wasm_bytes;
            byte_array->bytes_len = (uint32_t) wasm_file_length;
        }

        return byte_array;
    }
};

/**
 * Extract the data structure inside the `wasm_bytes` resource.
 */
wasmer_byte_array *wasm_bytes_from_resource(zend_resource *wasm_bytes_resource)
{
    wasm_lazy_byte_array *lazy_byte_array = (wasm_lazy_byte_array *) zend_fetch_resource(
        wasm_bytes_resource,
        wasm_bytes_resource_name,
        wasm_bytes_resource_number
    );

    return lazy_byte_array->get_bytes();
}

/**
 * Destructor for the `wasm_bytes` resource.
 */
static void wasm_bytes_destructor(zend_resource *resource)
{
    wasm_lazy_byte_array *lazy_byte_array = (wasm_lazy_byte_array *) zend_fetch_resource(
        resource,
        wasm_bytes_resource_name,
        wasm_bytes_resource_number
    );
    free(lazy_byte_array);
}

/**
 * Declare the parameter information for the `wasm_fetch_bytes` function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_fetch_bytes, ZEND_RETURN_VALUE, ARITY(1), IS_RESOURCE, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, file_path, IS_STRING, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_fetch_bytes` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * // `$bytes` is of type `resource of type (wasm_bytes)`.
 * ```
 *
 * Important note: The bytes are fetched, not read, when the function
 * is called. It means that the bytes are lazily read when other
 * functions need it, like `wasm_validate`, `wasm_compile` and
 * `wasm_new_instance`.
 */
PHP_FUNCTION(wasm_fetch_bytes)
{
    char *file_path;
    size_t file_path_length;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_PATH(file_path, file_path_length)
    ZEND_PARSE_PARAMETERS_END();

    wasm_lazy_byte_array *wasm_lazy_byte_array = new ::wasm_lazy_byte_array(file_path);

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) wasm_lazy_byte_array, wasm_bytes_resource_number);

    RETURN_RES(resource);
}

/**
 * Declare the parameter information for the `wasm_validate`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_validate, ZEND_RETURN_VALUE, ARITY(1), _IS_BOOL, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_bytes, IS_RESOURCE, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_validate` function.
 *
 * # Usage
 *
 * ```php
 $ $bytes = wasm_fetch_bytes('my_program.wasm');
 * $valid = wasm_validate($bytes);
 * ```
 */
PHP_FUNCTION(wasm_validate)
{
    zval *wasm_bytes_resource;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_RESOURCE(wasm_bytes_resource)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the bytes from the resource.
    wasmer_byte_array *wasm_byte_array = wasm_bytes_from_resource(Z_RES_P(wasm_bytes_resource));

    if (NULL == wasm_byte_array) {
        RETURN_FALSE;
    }

    // Check whether the bytes are valid or not.
    bool is_valid = wasmer_validate(wasm_byte_array->bytes, wasm_byte_array->bytes_len);

    RETURN_BOOL(is_valid);
}

/**
 * Information for the `wasm_module` resource.
 */
const char* wasm_module_resource_name;
int wasm_module_resource_number;

/**
 * Extract the data structure inside the `wasm_module` resource.
 */
wasmer_module_t *wasm_module_from_resource(zend_resource *wasm_module_resource)
{
    return (wasmer_module_t *) zend_fetch_resource(
        wasm_module_resource,
        wasm_module_resource_name,
        wasm_module_resource_number
    );
}

/**
 * Destructor for the `wasm_module` resource.
 */
static void wasm_module_destructor(zend_resource *resource)
{
    wasmer_module_t *wasm_module = wasm_module_from_resource(resource);

    if (wasm_module == NULL) {
        return;
    }

    wasmer_module_destroy(wasm_module);
}

/**
 * Declare the parameter information for the `wasm_compile`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_compile, ZEND_RETURN_VALUE, ARITY(1), IS_RESOURCE, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_bytes, IS_RESOURCE, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_module_unique_identifier, IS_STRING, NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_compile` function.
 *
 * # Usage
 *
 * Classical usage:
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes);
 * // `$module` is of type `resource of type (wasm_module)`.
 * ```
 *
 * If one wants to avoid to recompile the module each time, it is
 * possible to compute a persistent resource by passing a module
 * unique identifier string, e.g.:
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes, 'foo');
 * // `$module` is of type `resource of type (wasm_module)`, and persistent.
 * ```
 *
 * In the case above, further execution will **not** fetch the bytes
 * nor compile the module. Indeed, bytes are not fetched because they
 * are lazily fetch on-demand, and the module will not be re-compiled
 * because the resource is persistent.
 */
PHP_FUNCTION(wasm_compile)
{
    zval *wasm_bytes_resource;
    zend_string *wasm_module_unique_identifier = NULL;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 2)
        Z_PARAM_RESOURCE(wasm_bytes_resource)
        Z_PARAM_OPTIONAL
        Z_PARAM_STR_EX(wasm_module_unique_identifier, NULLABLE, 0);
    ZEND_PARSE_PARAMETERS_END();

    zend_string *resource_key = NULL;
    bool persistent_wasm_module = false;

    // The Wasm module resource will be persistent if there is a unique identifier.
    if (wasm_module_unique_identifier != NULL) {
        resource_key = zend_string_copy(wasm_module_unique_identifier);
        persistent_wasm_module = true;
    }

    zend_resource *resource = NULL;

    // Wasm module persistent resource look up.
    if (persistent_wasm_module) {
        resource = (zend_resource *) zend_hash_find_ptr(&EG(persistent_list), resource_key);
    }

    // Wasm module persistent resource is disabled, or it is not
    // registered in the persistent resource registry.
    if (resource == NULL) {
        // Extract the bytes from the resource.
        wasmer_byte_array *wasm_byte_array = wasm_bytes_from_resource(Z_RES_P(wasm_bytes_resource));

        if (NULL == wasm_byte_array) {
            RETURN_NULL();
        }

        // Create a new Wasm module.
        wasmer_module_t *wasm_module = NULL;
        wasmer_result_t wasm_compilation_result = wasmer_compile(
            &wasm_module,
            // Bytes.
            (uint8_t *) wasm_byte_array->bytes,
            // Bytes length.
            wasm_byte_array->bytes_len
        );

        // Compilation failed.
        if (wasm_compilation_result != wasmer_result_t::WASMER_OK) {
            free(wasm_module);

            RETURN_NULL();
        }

        // Store the module in a persistent resource.
        if (persistent_wasm_module) {
            resource = zend_register_persistent_resource_ex(
                resource_key,
                (void *) wasm_module,
                wasm_module_resource_number
            );
        }
        // Store the module in a regular resource.
        else {
            resource = zend_register_resource((void *) wasm_module, wasm_module_resource_number);
        }

        if (resource == NULL) {
            RETURN_NULL();
        }
    }
    // The resource is already registered.
    else {}

    RETURN_RES(resource);
}

/**
 * Clean up all persistent resources registered by this module.
 */
static int clean_up_persistent_resources(zval *hashmap_item)
{
    zend_resource *resource = Z_RES_P(hashmap_item);

    if (resource->type == wasm_module_resource_number) {
        wasm_module_destructor(resource);
        return ZEND_HASH_APPLY_REMOVE;
    }

    return ZEND_HASH_APPLY_KEEP;
}

/**
 * Iterate over the persistent resources list to clean up Wasm
 * persistent resources.
 */
static void php_wasm_module_clean_up_persistent_resources()
{
    zend_hash_apply(&EG(persistent_list), (apply_func_t) clean_up_persistent_resources);
}

/**
 * Declare the parameter information for the `wasm_module_clean_up_persistent_resources`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_clean_up_persistent_resources, ZEND_RETURN_VALUE, ARITY(0), IS_VOID, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_module_clean_up_persistent_resources` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes, 'foo');
 * // The module is registered as a persistent resource.
 *
 * wasm_module_clean_up_persistent_resource();
 *
 * $module = wasm_compile($bytes, 'foo');
 * // The module is registered as a persistent resource, again.
 * ```
 */
PHP_FUNCTION(wasm_module_clean_up_persistent_resources)
{
    ZEND_PARSE_PARAMETERS_NONE();

    // Clean up persistent resources.
    php_wasm_module_clean_up_persistent_resources();
}

/**
 * Declare the parameter information for the `wasm_module_serialize`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_serialize, ZEND_RETURN_VALUE, ARITY(1), IS_STRING, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_module, IS_RESOURCE, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_module_serialize` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes);
 * $serialized_module = wasm_module_serialize($module);
 * // `$serialized_module` is of type `string`.
 * ```
 */
PHP_FUNCTION(wasm_module_serialize)
{
    zval *wasm_module_resource;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_RESOURCE(wasm_module_resource)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the module from the resource.
    wasmer_module_t *wasm_module = wasm_module_from_resource(Z_RES_P(wasm_module_resource));

    if (wasm_module == NULL) {
        RETURN_NULL();
    }

    // Let's serialize the module.
    wasmer_serialized_module_t *wasm_serialized_module = NULL;

    if (wasmer_module_serialize(&wasm_serialized_module, wasm_module) != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    // Extract the bytes from the serialized module.
    wasmer_byte_array wasm_serialized_module_bytes = wasmer_serialized_module_bytes(wasm_serialized_module);

    ZVAL_STRINGL(
        return_value,
        (char *) (uint8_t *) wasm_serialized_module_bytes.bytes,
        wasm_serialized_module_bytes.bytes_len
    );
}

/**
 * Declare the parameter information for the `wasm_module_deserialize`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_deserialize, ZEND_RETURN_VALUE, ARITY(1), IS_RESOURCE, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_serialized_module, IS_STRING, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_module_deserialize` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes);
 * $serialized_module = wasm_module_serialize($module);
 * $module = wasm_module_deserialize($serialized_module);
 * ```
 */
PHP_FUNCTION(wasm_module_deserialize)
{
    char *wasm_serialized_module_bytes;
    size_t wasm_serialized_module_bytes_length;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_STRING(wasm_serialized_module_bytes, wasm_serialized_module_bytes_length)
    ZEND_PARSE_PARAMETERS_END();

    wasmer_serialized_module_t *wasm_serialized_module = NULL;

    if (wasmer_serialized_module_from_bytes(&wasm_serialized_module, (const uint8_t *) wasm_serialized_module_bytes, wasm_serialized_module_bytes_length) != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    wasmer_module_t *wasm_module = NULL;

    if (wasmer_module_deserialize(&wasm_module, wasm_serialized_module) != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) wasm_module, wasm_module_resource_number);

    RETURN_RES(resource);
}

/**
 * Information for the `wasm_instance` resource.
 */
const char* wasm_instance_resource_name;
int wasm_instance_resource_number;

/**
 * Extract the data structure inside the `wasm_instance` resource.
 */
wasmer_instance_t *wasm_instance_from_resource(zend_resource *wasm_instance_resource)
{
    return (wasmer_instance_t *) zend_fetch_resource(
        wasm_instance_resource,
        wasm_instance_resource_name,
        wasm_instance_resource_number
    );
}

/**
 * Destructor for the `wasm_instance` resource.
 */
static void wasm_instance_destructor(zend_resource *resource)
{
    wasmer_instance_t *wasm_instance = wasm_instance_from_resource(resource);

    if (wasm_instance == NULL) {
        return;
    }

    wasmer_instance_destroy(wasm_instance);
}

/**
 * Declare the parameter information for the
 * `wasm_module_new_instance` function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_module_new_instance, ZEND_RETURN_VALUE, ARITY(1), IS_RESOURCE, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_module, IS_RESOURCE, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_module_new_instance` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $module = wasm_compile($bytes);
 * $instance = wasm_module_new_instance($module);
 * // `$instance` is of type `resource of type (wasm_instance)`.
 * ```
 *
 * It is similar to running:
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $instance = wasm_new_instance($bytes);
 * ```
 */
PHP_FUNCTION(wasm_module_new_instance)
{
    zval *wasm_module_resource;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_RESOURCE(wasm_module_resource)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the module from the resource.
    wasmer_module_t *wasm_module = wasm_module_from_resource(Z_RES_P(wasm_module_resource));

    if (wasm_module == NULL) {
        RETURN_NULL();
    }

    // Create a new Wasm instance.
    wasmer_instance_t *wasm_instance = NULL;
    wasmer_result_t wasm_instantiation_result = wasmer_module_instantiate(
        // Module.
        wasm_module,
        // Instance.
        &wasm_instance,
        // Imports.
        {},
        // Imports length.
        0
    );

    // Instantiation failed.
    if (wasm_instantiation_result != wasmer_result_t::WASMER_OK) {
        free(wasm_instance);

        RETURN_NULL();
    }

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) wasm_instance, wasm_instance_resource_number);

    RETURN_RES(resource);
}

/**
 * Declare the parameter information for the `wasm_new_instance`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_new_instance, ZEND_RETURN_VALUE, ARITY(1), IS_RESOURCE, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_bytes, IS_RESOURCE, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_new_instance` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $instance = wasm_new_instance($bytes);
 * // `$instance` is of type `resource of type (wasm_instance)`.
 * ```
 *
 * This function is a shortcut of `wasm_compile` +
 * `wasm_module_new_instance`. It “hides” the module compilation step.
 */
PHP_FUNCTION(wasm_new_instance)
{
    zval *wasm_bytes_resource;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_RESOURCE(wasm_bytes_resource)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the bytes from the resource.
    wasmer_byte_array *wasm_byte_array = wasm_bytes_from_resource(Z_RES_P(wasm_bytes_resource));

    if (NULL == wasm_byte_array) {
        RETURN_NULL();
    }

    // Create a new Wasm instance.
    wasmer_instance_t *wasm_instance = NULL;
    wasmer_result_t wasm_instantiation_result = wasmer_instantiate(
        &wasm_instance,
        // Bytes.
        (uint8_t *) wasm_byte_array->bytes,
        // Bytes length.
        wasm_byte_array->bytes_len,
        // Imports.
        {},
        // Imports length.
        0
    );

    // Instantiation failed.
    if (wasm_instantiation_result != wasmer_result_t::WASMER_OK) {
        free(wasm_instance);

        RETURN_NULL();
    }

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) wasm_instance, wasm_instance_resource_number);

    RETURN_RES(resource);
}

/**
 * Declare the parameter information for the
 * `wasm_get_function_signature` function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_get_function_signature, ZEND_RETURN_VALUE, ARITY(2), IS_ARRAY, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_instance, IS_RESOURCE, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, function_name, IS_STRING, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_get_function_signature` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $instance = wasm_new_instance($bytes);
 * $signature = wasm_get_function_signature($instance, 'function_name');
 * // `$signature` is an array of `WASM_TYPE_*` constants. The first
 * // entries are for the inputs, the last entry is for the output.
 * ```
 */
PHP_FUNCTION(wasm_get_function_signature)
{
    zval *wasm_instance_resource;
    char *function_name;
    size_t function_name_length;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
        Z_PARAM_RESOURCE(wasm_instance_resource)
        Z_PARAM_STRING(function_name, function_name_length)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the Wasm instance from the resource.
    wasmer_instance_t *wasm_instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));

    if (NULL == wasm_instance) {
        RETURN_NULL();
    }

    // Read all the export definitions (of all kinds).
    wasmer_exports_t *wasm_exports = NULL;

    wasmer_instance_exports(wasm_instance, &wasm_exports);

    int number_of_exports = wasmer_exports_len(wasm_exports);

    // There is no export definition.
    if (number_of_exports == 0) {
        wasmer_exports_destroy(wasm_exports);

        RETURN_NULL();
    }

    // Look for a function of the given name in the export definitions.
    const wasmer_export_func_t *wasm_function = NULL;

    for (uint32_t nth = 0; nth < number_of_exports; ++nth) {
        wasmer_export_t *wasm_export = wasmer_exports_get(wasm_exports, nth);
        wasmer_import_export_kind wasm_export_kind = wasmer_export_kind(wasm_export);

        // Not a function definition, let's continue.
        if (wasm_export_kind != wasmer_import_export_kind::WASM_FUNCTION) {
            continue;
        }

        // Read the export name.
        wasmer_byte_array wasm_export_name = wasmer_export_name(wasm_export);

        if (wasm_export_name.bytes_len != function_name_length) {
            continue;
        }

        // Gotcha?
        if (strncmp(function_name, (const char *) wasm_export_name.bytes, wasm_export_name.bytes_len) == 0) {
            wasm_function = wasmer_export_to_func(wasm_export);

            break;
        }
    }

    // No function with the given name has been found.
    if (wasm_function == NULL) {
        wasmer_exports_destroy(wasm_exports);

        RETURN_NULL();
    }

    // Read the number of inputs.
    uint32_t wasm_function_inputs_arity;

    if (wasmer_export_func_params_arity(wasm_function, &wasm_function_inputs_arity) != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    // Prepare the result of this function.
    array_init_size(return_value, wasm_function_inputs_arity + /* output */ 1);

    // Read the input types.
    wasmer_value_tag *wasm_function_input_signatures = (wasmer_value_tag *) malloc(sizeof(wasmer_value_tag) * wasm_function_inputs_arity);

    if (wasmer_export_func_params(wasm_function, wasm_function_input_signatures, wasm_function_inputs_arity) != wasmer_result_t::WASMER_OK) {
        free(wasm_function_input_signatures);

        RETURN_NULL();
    }

    for (uint32_t nth = 0; nth < wasm_function_inputs_arity; ++nth) {
        // Add to the result.
        add_next_index_long(return_value, (zend_long) wasm_function_input_signatures[nth]);
    }

    free(wasm_function_input_signatures);

    // Read the number of outputs.
    uint32_t wasm_function_outputs_arity;

    if (wasmer_export_func_returns_arity(wasm_function, &wasm_function_outputs_arity) != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    // PHP only expects one output, i.e. out returned value.
    if (wasm_function_outputs_arity == 0) {
        RETURN_NULL();
    }

    // Read the output types.
    wasmer_value_tag *wasm_function_output_signatures = (wasmer_value_tag *) malloc(sizeof(wasmer_value_tag) * wasm_function_outputs_arity);

    if (wasmer_export_func_returns(wasm_function, wasm_function_output_signatures, wasm_function_outputs_arity) != wasmer_result_t::WASMER_OK) {
        free(wasm_function_output_signatures);

        RETURN_NULL();
    }

    // Add to the result.
    add_next_index_long(return_value, (zend_long) wasm_function_output_signatures[0]);

    free(wasm_function_output_signatures);
    wasmer_exports_destroy(wasm_exports);

    // The result is automatically returned (magic, see the `PHP_FUNCTION` macro).
}

/**
 * Information for the `wasm_value` resource.
 */
const char* wasm_value_resource_name;
int wasm_value_resource_number;

/**
 * Extract the data structure inside the `wasm_value` resource.
 */
wasmer_value_t *wasm_value_from_resource(zend_resource *wasm_value_resource)
{
    return (wasmer_value_t *) zend_fetch_resource(
        wasm_value_resource,
        wasm_value_resource_name,
        wasm_value_resource_number
    );
}

/**
 * Destructor for the `wasm_value` resource.
 */
static void wasm_value_destructor(zend_resource *resource)
{
    wasmer_value_t *wasm_value = wasm_value_from_resource(resource);

    if (wasm_value == NULL) {
        return;
    }

    efree(wasm_value);
}

/**
 * Declare the parameter information for the `wasm_value` function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_value, ZEND_RETURN_VALUE, ARITY(2), IS_RESOURCE, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, type, IS_LONG, NOT_NULLABLE)
    ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_value` function.
 *
 * # Usage
 *
 * ```php
 * $value = wasm_value(WASM_TYPE_I32, 7);
 * // `$value` is of type `resource of type (wasm_value)`.
 * ```
 */
PHP_FUNCTION(wasm_value)
{
    zend_long value_type;
    zval *value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
        Z_PARAM_LONG(value_type)
        Z_PARAM_ZVAL(value)
    ZEND_PARSE_PARAMETERS_END();

    if (value_type < 0) {
        RETURN_NULL();
    }

    // Convert the value to a `wasmer_value_tag`. It is expected to
    // receive a `WASM_TYPE_*` constant.
    wasmer_value_tag type = (wasmer_value_tag) (uint32_t) value_type;
    wasmer_value_t *wasm_value = (wasmer_value_t *) emalloc(sizeof(wasmer_value_t));

    // Convert the PHP value to a `wasm_value_t`.
    if (type == wasmer_value_tag::WASM_I32) {
        wasm_value->tag = type;
        wasm_value->value.I32 = (int32_t) value->value.lval;
    } else if (type == wasmer_value_tag::WASM_I64) {
        wasm_value->tag = type;
        wasm_value->value.I64 = (int64_t) value->value.lval;
    } else if (type == wasmer_value_tag::WASM_F32) {
        wasm_value->tag = type;
        wasm_value->value.F32 = (float) value->value.dval;
    } else if (type == wasmer_value_tag::WASM_F64) {
        wasm_value->tag = type;
        wasm_value->value.F64 = (double) value->value.dval;
    }
    // Invalid value type provided.
    else {
        efree(wasm_value);

        RETURN_NULL();
    }

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) wasm_value, wasm_value_resource_number);

    RETURN_RES(resource);
}

/**
 * Declare the parameter information for the `wasm_invoke_function`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_invoke_function, ZEND_RETURN_VALUE, ARITY(3), _IS_NUMBER, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_instance, IS_RESOURCE, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, function_name, IS_STRING, NOT_NULLABLE)
    ZEND_ARG_ARRAY_INFO(0, inputs, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_invoke_function` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $instance = wasm_new_instance($bytes);
 *
 * // sum(1, 2)
 * $result = wasm_invoke_function(
 *     $instance,
 *     'sum',
 *     [
 *         wasm_value(WASM_TYPE_I32, 1),
 *         wasm_value(WASM_TYPE_I32, 2),
 *     ]
 * );
 * ```
 */
PHP_FUNCTION(wasm_invoke_function)
{
    zval *wasm_instance_resource;
    char *function_name;
    size_t function_name_length;
    HashTable *inputs;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 3, 3)
        Z_PARAM_RESOURCE(wasm_instance_resource)
        Z_PARAM_STRING(function_name, function_name_length)
        Z_PARAM_ARRAY_HT(inputs)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the Wasm instance from the resource.
    wasmer_instance_t *wasm_instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));

    if (NULL == wasm_instance) {
        RETURN_NULL();
    }

    // Read the number of inputs.
    size_t function_input_length = zend_hash_num_elements(inputs);

    // Extract the input values from the `wasm_value` resources.
    wasmer_value_t *function_inputs = (wasmer_value_t *) emalloc(sizeof(wasmer_value_t) * function_input_length);

    {
        zend_ulong key;
        zval *value;

        ZEND_HASH_FOREACH_NUM_KEY_VAL(inputs, key, value)
            function_inputs[key] = *wasm_value_from_resource(Z_RES_P(value));
        ZEND_HASH_FOREACH_END();
    }

    // PHP expects one output, i.e. returned value.
    size_t function_output_length = 1;
    wasmer_value_t output;
    wasmer_value_t function_outputs[] = {output};

    // Call the Wasm function.
    wasmer_result_t function_call_result = wasmer_instance_call(
        // Instance.
        wasm_instance,
        // Function name.
        function_name,
        // Inputs.
        function_inputs,
        function_input_length,
        // Outputs.
        function_outputs,
        function_output_length
    );

    efree(function_inputs);

    // Failed to call the Wasm function.
    if (function_call_result != wasmer_result_t::WASMER_OK) {
        RETURN_NULL();
    }

    // Read the first output, because PHP expects only one output, as
    // said above.
    wasmer_value_t function_output = function_outputs[0];

    // Convert the Wasm value to a PHP value.
    if (function_output.tag == wasmer_value_tag::WASM_I32) {
        RETURN_LONG(function_output.value.I32);
    } else if (function_output.tag == wasmer_value_tag::WASM_I64) {
        RETURN_LONG(function_output.value.I64);
    } else if (function_output.tag == wasmer_value_tag::WASM_F32) {
        RETURN_DOUBLE(function_output.value.F32);
    } else if (function_output.tag == wasmer_value_tag::WASM_F64) {
        RETURN_DOUBLE(function_output.value.F64);
    } else {
        RETURN_NULL();
    }
}

/**
 * Declare the parameter information for the `wasm_get_last_error`
 * function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasm_get_last_error, ZEND_RETURN_VALUE, ARITY(0), IS_STRING, NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_get_last_error` function.
 *
 * # Usage
 *
 * ```php
 $ $error = wasm_get_last_error();
 * ```
 */
PHP_FUNCTION(wasm_get_last_error)
{
    ZEND_PARSE_PARAMETERS_NONE();

    int error_message_length = wasmer_last_error_length();

    if (error_message_length == 0) {
        RETURN_NULL();
    }

    char *error_message = (char *) emalloc(error_message_length);
    wasmer_last_error_message(error_message, error_message_length);

    ZVAL_STRINGL(return_value, error_message, error_message_length - 1);
}

// Declare the functions with their information.
static const zend_function_entry wasm_functions[] = {
    PHP_FE(wasm_fetch_bytes,							arginfo_wasm_fetch_bytes)
    PHP_FE(wasm_validate,								arginfo_wasm_validate)
    PHP_FE(wasm_compile,								arginfo_wasm_compile)
    PHP_FE(wasm_module_clean_up_persistent_resources,	arginfo_wasm_module_clean_up_persistent_resources)
    PHP_FE(wasm_module_new_instance,					arginfo_wasm_module_new_instance)
    PHP_FE(wasm_module_serialize,						arginfo_wasm_module_serialize)
    PHP_FE(wasm_module_deserialize,						arginfo_wasm_module_deserialize)
    PHP_FE(wasm_new_instance,							arginfo_wasm_new_instance)
    PHP_FE(wasm_get_function_signature,					arginfo_wasm_get_function_signature)
    PHP_FE(wasm_value,									arginfo_wasm_value)
    PHP_FE(wasm_invoke_function,						arginfo_wasm_invoke_function)
    PHP_FE(wasm_get_last_error,							arginfo_wasm_get_last_error)
    PHP_FE_END
};

/**
 * Custom object for the `WasmArrayBuffer` class.
 */
typedef struct {
    // The internal buffer.
    void *buffer;

    // The internal buffer length.
    size_t buffer_length;

    // The class instance, i.e. the object. It must be the last item
    // of the structure.
    zend_object instance;
} wasm_array_buffer_object;

zend_class_entry *wasm_array_buffer_class_entry;
zend_object_handlers wasm_array_buffer_class_entry_handlers;

/**
 * Gets the `wasm_array_buffer_object` pointer from a `zend_object` pointer.
 */
static inline wasm_array_buffer_object *wasm_array_buffer_object_from_zend_object(zend_object *object)
{
	return (wasm_array_buffer_object *) ((char *)(object) - XtOffsetOf(wasm_array_buffer_object, instance));
}

/**
 * Function for a `zend_class_entry` to create a `WasmArrayBuffer` object.
 */
static zend_object *create_wasm_array_buffer_object(zend_class_entry *class_entry)
{
    wasm_array_buffer_object *wasm_array_buffer = (wasm_array_buffer_object *) ecalloc(
        1,
        sizeof(wasm_array_buffer_object) + zend_object_properties_size(class_entry)
    );
    wasm_array_buffer->buffer = NULL;
    wasm_array_buffer->buffer_length = 0;

    zend_object_std_init(&wasm_array_buffer->instance, class_entry);
    object_properties_init(&wasm_array_buffer->instance, class_entry);

    wasm_array_buffer->instance.handlers = &wasm_array_buffer_class_entry_handlers;

    return &wasm_array_buffer->instance;
}

/**
 * Handler for a `zend_class_entry` to destroy (i.e. call the
 * destructor on the userland) a `WasmArrayBuffer` object.
 */
static void destroy_wasm_array_buffer_object(zend_object *object)
{
    zend_objects_destroy_object(object);
}

/**
 * Handler for a `zend_class_entry` to free a `WasmArrayBuffer` object.
 */
static void free_wasm_array_buffer_object(zend_object *object)
{
    wasm_array_buffer_object *wasm_array_buffer_object = wasm_array_buffer_object_from_zend_object(object);

    if (wasm_array_buffer_object->buffer != NULL) {
        free(wasm_array_buffer_object->buffer);
    }

    zend_object_std_dtor(object);
}

// Shortcut to get `$this` in a `WasmArrayBuffer` method.
#define WASM_ARRAY_BUFFER_OBJECT_THIS() wasm_array_buffer_object_from_zend_object(Z_OBJ_P(getThis()))

/**
 * Declare the parameter information for the
 * `WasmArrayBuffer::__construct` method.
 */
ZEND_BEGIN_ARG_INFO_EX(arginfo_wasmarraybuffer___construct, 0, ZEND_RETURN_VALUE, ARITY(1))
    ZEND_ARG_TYPE_INFO(0, byte_length, IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmArrayBuffer::__construct` method.
 *
 * # Usage
 *
 * ```php
 $ $buffer = new WasmArrayBuffer();
 * ```
 */
PHP_METHOD(WasmArrayBuffer, __construct)
{
    zend_long byte_length;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(byte_length)
    ZEND_PARSE_PARAMETERS_END();

    if (byte_length <= 0) {
        zend_throw_exception(zend_ce_exception, "Buffer length must be positive.", 0);
        return;
    }

    wasm_array_buffer_object *wasm_array_buffer_object = WASM_ARRAY_BUFFER_OBJECT_THIS();
    wasm_array_buffer_object->buffer = calloc(byte_length, byte_length);
    wasm_array_buffer_object->buffer_length = (size_t) byte_length;
}

/**
 * Declare the parameter information for the
 * `WasmArrayBuffer::getByteLength` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmarraybuffer_get_byte_length, ZEND_RETURN_VALUE, ARITY(0), IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmArrayBuffer::getByteLength` method.
 *
 * # Usage
 *
 * ```php
 * $length = 42;
 * $buffer = new WasmArrayBuffer($length);
 * assert($buffer->getByteLength() === $length);
 * ```
 */
PHP_METHOD(WasmArrayBuffer, getByteLength)
{
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_array_buffer_object *wasm_array_buffer_object = WASM_ARRAY_BUFFER_OBJECT_THIS();

    RETURN_LONG(wasm_array_buffer_object->buffer_length);
}

// Declare the methods with their information.
static const zend_function_entry wasm_array_buffer_methods[] = {
    PHP_ME(WasmArrayBuffer, __construct,	arginfo_wasmarraybuffer___construct, ZEND_ACC_PUBLIC)
    PHP_ME(WasmArrayBuffer, getByteLength,	arginfo_wasmarraybuffer_get_byte_length, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

// Module initialization event.
PHP_MINIT_FUNCTION(wasm)
{
    // Declare the constants.
    REGISTER_LONG_CONSTANT("WASM_TYPE_I32", (zend_long) wasmer_value_tag::WASM_I32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_TYPE_I64", (zend_long) wasmer_value_tag::WASM_I64, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_TYPE_F32", (zend_long) wasmer_value_tag::WASM_F32, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT("WASM_TYPE_F64", (zend_long) wasmer_value_tag::WASM_F64, CONST_CS | CONST_PERSISTENT);

    // Declare the `wasm_bytes` resource.
    wasm_bytes_resource_name = "wasm_bytes";
    wasm_bytes_resource_number = zend_register_list_destructors_ex(
        wasm_bytes_destructor,
        NULL,
        wasm_bytes_resource_name,
        module_number
    );

    // Declare the `wasm_module` resource.
    wasm_module_resource_name = "wasm_module";
    wasm_module_resource_number = zend_register_list_destructors_ex(
        wasm_module_destructor,
        wasm_module_destructor,
        wasm_module_resource_name,
        module_number
    );

    // Declare the `wasm_instance` resource.
    wasm_instance_resource_name = "wasm_instance";
    wasm_instance_resource_number = zend_register_list_destructors_ex(
        wasm_instance_destructor,
        NULL,
        wasm_instance_resource_name,
        module_number
    );

    // Declare the `wasm_value` resource.
    wasm_value_resource_name = "wasm_value";
    wasm_value_resource_number = zend_register_list_destructors_ex(
        wasm_value_destructor,
        NULL,
        wasm_value_resource_name,
        module_number
    );

    // Declare the `WasmArrayBuffer` class.
    zend_class_entry class_entry;

    INIT_CLASS_ENTRY(class_entry, "WasmArrayBuffer", wasm_array_buffer_methods);
    wasm_array_buffer_class_entry = zend_register_internal_class(&class_entry TSRMLS_CC);
    wasm_array_buffer_class_entry->create_object = create_wasm_array_buffer_object;
    wasm_array_buffer_class_entry->ce_flags |= ZEND_ACC_FINAL;

    memcpy(&wasm_array_buffer_class_entry_handlers, zend_get_std_object_handlers(), sizeof(wasm_array_buffer_class_entry_handlers));
    wasm_array_buffer_class_entry_handlers.offset = XtOffsetOf(wasm_array_buffer_object, instance);
    wasm_array_buffer_class_entry_handlers.dtor_obj = destroy_wasm_array_buffer_object;
    wasm_array_buffer_class_entry_handlers.free_obj = free_wasm_array_buffer_object;
    wasm_array_buffer_class_entry_handlers.clone_obj = NULL;

    return SUCCESS;
}

// Initialize the module information.
PHP_MINFO_FUNCTION(wasm)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "wasm support", "enabled");
    php_info_print_table_end();
}

// Request initialization event.
PHP_RINIT_FUNCTION(wasm)
{
#if defined(ZTS) && defined(COMPILE_DL_WASM)
    ZEND_TSRMLS_CACHE_UPDATE();
#endif

    return SUCCESS;
}

// Request shutdown event.
PHP_RSHUTDOWN_FUNCTION(wasm)
{
	return SUCCESS;
}

// Module shutdown event.
PHP_MSHUTDOWN_FUNCTION(wasm)
{
    // Clean up persistent resources.
    php_wasm_module_clean_up_persistent_resources();

    return SUCCESS;
}

// Last boilerplate.
zend_module_entry wasm_module_entry = {
    STANDARD_MODULE_HEADER,
    "wasm",					/* Extension name */
    wasm_functions,			/* zend_function_entry */
    PHP_MINIT(wasm),		/* PHP_MINIT - Module initialization */
    PHP_MSHUTDOWN(wasm),	/* PHP_MSHUTDOWN - Module shutdown */
    PHP_RINIT(wasm),		/* PHP_RINIT - Request initialization */
    PHP_RSHUTDOWN(wasm),	/* PHP_RSHUTDOWN - Request shutdown */
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
