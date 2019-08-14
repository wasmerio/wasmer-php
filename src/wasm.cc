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

#include "wasm.hh"
#include <unordered_map>
#include <string>

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
    wasm_array_buffer->memory = NULL;
    wasm_array_buffer->buffer = NULL;
    wasm_array_buffer->buffer_length = 0;
    wasm_array_buffer->allocated_buffer = true;

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

    if (wasm_array_buffer_object->memory != NULL) {
        wasm_array_buffer_object->memory = NULL;
    }

    if (wasm_array_buffer_object->allocated_buffer &&
        wasm_array_buffer_object->buffer != NULL) {
        free(wasm_array_buffer_object->buffer);
    }

    zend_object_std_dtor(object);
}

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
 * $buffer = new WasmArrayBuffer(256);
 * ```
 */
PHP_METHOD(WasmArrayBuffer, __construct)
{
    zend_long byte_length;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(byte_length)
    ZEND_PARSE_PARAMETERS_END();

    if (byte_length <= 0) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Buffer length must be positive; given %lld.", byte_length);

        return;
    }

    // Allocate a new buffer, and assign it the `wasm_array_buffer_object`.
    // This new buffer is initialized with zero-bytes (see `calloc`).
    wasm_array_buffer_object *wasm_array_buffer_object = WASM_ARRAY_BUFFER_OBJECT_THIS();
    wasm_array_buffer_object->buffer = (int8_t *) calloc(byte_length, byte_length);
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

/**
 * Declare the parameter information for the
 * `WasmArrayBuffer::grow` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmarraybuffer_grow, ZEND_RETURN_VALUE, ARITY(1), IS_VOID, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, number_of_pages, IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmArrayBuffer::grow` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $buffer->grow(1);
 * ```
 */
PHP_METHOD(WasmArrayBuffer, grow)
{
    zend_long number_of_pages;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(number_of_pages)
    ZEND_PARSE_PARAMETERS_END();

    if (number_of_pages <= 0) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Number of pages must be positive; given %lld.", number_of_pages);

        RETURN_NULL();
    }

    wasm_array_buffer_object *wasm_array_buffer_object = WASM_ARRAY_BUFFER_OBJECT_THIS();

    if (wasm_array_buffer_object->allocated_buffer == true) {
        RETURN_NULL();
    }

    wasmer_result_t wasm_memory_grow_result = wasmer_memory_grow(
        wasm_array_buffer_object->memory,
        // Number of pages.
        (uint32_t) number_of_pages
    );

    // Compilation failed.
    if (wasm_memory_grow_result != wasmer_result_t::WASMER_OK) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Failed to grow the memory.");

        RETURN_NULL();
    }

    // Refresh the memory data and its length.
    uint8_t *wasm_memory_data = wasmer_memory_data(wasm_array_buffer_object->memory);
    uint32_t wasm_memory_data_length = wasmer_memory_data_length(wasm_array_buffer_object->memory);

    // Reset the internal buffer of `WasmArrayBuffer`.
    wasm_array_buffer_object->buffer = (int8_t *) wasm_memory_data;
    wasm_array_buffer_object->buffer_length = (size_t) wasm_memory_data_length;
}

// Declare the methods of the `WasmArrayBuffer` class with their information.
static const zend_function_entry wasm_array_buffer_methods[] = {
    PHP_ME(WasmArrayBuffer, __construct,	arginfo_wasmarraybuffer___construct, ZEND_ACC_PUBLIC)
    PHP_ME(WasmArrayBuffer, getByteLength,	arginfo_wasmarraybuffer_get_byte_length, ZEND_ACC_PUBLIC)
    PHP_ME(WasmArrayBuffer, grow,			arginfo_wasmarraybuffer_grow, ZEND_ACC_PUBLIC)
    PHP_FE_END
};

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
        if (byte_array != NULL) {
            efree((uint8_t *) byte_array->bytes);
        }
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
 * $bytes = wasm_fetch_bytes('my_program.wasm');
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
 * Given an already instantiated `wasm_instance`, this function will
 * fill the other fields (`exports` and `exported_functions`).
 */
void initialize_wasm_instance(wasm_instance *instance)
{
    wasmer_instance_exports(instance->instance, &instance->exports);

    {
        int number_of_exports = wasmer_exports_len(instance->exports);
        auto exported_functions = new std::unordered_map<std::string, wasm_exported_function *>();

        for (uint32_t nth = 0; nth < number_of_exports; ++nth) {
            wasmer_export_t *wasm_export = wasmer_exports_get(instance->exports, nth);
            wasmer_import_export_kind wasm_export_kind = wasmer_export_kind(wasm_export);

            // Not a function definition, let's continue.
            if (wasm_export_kind != wasmer_import_export_kind::WASM_FUNCTION) {
                continue;
            }

            // Read the export name.
            wasmer_byte_array wasm_export_name = wasmer_export_name(wasm_export);

            const wasmer_export_func_t *wasm_function = wasmer_export_to_func(wasm_export);

            // Read the number of inputs.
            uint32_t wasm_function_inputs_arity;

            if (wasmer_export_func_params_arity(wasm_function, &wasm_function_inputs_arity) != wasmer_result_t::WASMER_OK) {
                zend_throw_exception_ex(
                    zend_ce_exception,
                    0,
                    "Failed to read the input arity of the `%.*s` exported function.",
                    (int) wasm_export_name.bytes_len,
                    wasm_export_name.bytes
                );

                return;
            }

            // Read the input types.
            wasmer_value_tag *wasm_function_input_signatures = (wasmer_value_tag *) malloc(sizeof(wasmer_value_tag) * wasm_function_inputs_arity);

            if (wasmer_export_func_params(wasm_function, wasm_function_input_signatures, wasm_function_inputs_arity) != wasmer_result_t::WASMER_OK) {
                free(wasm_function_input_signatures);

                zend_throw_exception_ex(
                    zend_ce_exception,
                    0,
                    "Failed to read the signature of the `%.*s` exported function.",
                    (int) wasm_export_name.bytes_len,
                    wasm_export_name.bytes
                );

                return;
            }

            // Read the number of outputs.
            uint32_t wasm_function_outputs_arity;

            if (wasmer_export_func_returns_arity(wasm_function, &wasm_function_outputs_arity) != wasmer_result_t::WASMER_OK) {
                free(wasm_function_input_signatures);

                zend_throw_exception_ex(
                    zend_ce_exception,
                    0,
                    "Failed to read the output arity of the `%.*s` exported function.",
                    (int) wasm_export_name.bytes_len,
                    wasm_export_name.bytes
                );

                return;
            }

            // Read the output types.
            wasmer_value_tag *wasm_function_output_signatures = (wasmer_value_tag *) malloc(sizeof(wasmer_value_tag) * wasm_function_outputs_arity);

            if (wasmer_export_func_returns(wasm_function, wasm_function_output_signatures, wasm_function_outputs_arity) != wasmer_result_t::WASMER_OK) {
                free(wasm_function_input_signatures);
                free(wasm_function_output_signatures);

                zend_throw_exception_ex(
                    zend_ce_exception,
                    0,
                    "Failed to read the signature of the `%.*s` exported function.",
                    (int) wasm_export_name.bytes_len,
                    wasm_export_name.bytes
                );

                return;
            }

            auto wasm_exported_func = (wasm_exported_function *) emalloc(sizeof(wasm_exported_function));
            wasm_exported_func->exported_function = wasm_function;
            wasm_exported_func->inputs = wasm_function_input_signatures;
            wasm_exported_func->input_arity = wasm_function_inputs_arity;
            wasm_exported_func->outputs = wasm_function_output_signatures;
            wasm_exported_func->output_arity = wasm_function_outputs_arity;

            std::string wasm_function_name = std::string((const char *) wasm_export_name.bytes, (size_t) wasm_export_name.bytes_len);
            (*exported_functions)[wasm_function_name] = wasm_exported_func;
        }

        instance->exported_functions = exported_functions;
    }
}

/**
 * Extract the data structure inside the `wasm_instance` resource.
 */
wasm_instance *wasm_instance_from_resource(zend_resource *wasm_instance_resource)
{
    return (wasm_instance *) zend_fetch_resource(
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
    wasm_instance *instance = wasm_instance_from_resource(resource);

    if (instance == NULL) {
        return;
    }

    for (
         auto iterator = instance->exported_functions->begin();
         iterator != instance->exported_functions->end();
         ++iterator
    ) {
        wasm_exported_function *exported_function = iterator->second;
        free(exported_function->inputs);
        free(exported_function->outputs);
    }

    instance->exported_functions->clear();
    delete instance->exported_functions;

    wasmer_exports_destroy(instance->exports);
    wasmer_instance_destroy(instance->instance);
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
    wasm_instance *instance = (wasm_instance *) emalloc(sizeof(wasm_instance));
    instance->instance = NULL;
    instance->exports = NULL;
    instance->exported_functions = NULL;

    wasmer_result_t wasm_instantiation_result = wasmer_module_instantiate(
        // Module.
        wasm_module,
        // Instance.
        &instance->instance,
        // Imports.
        {},
        // Imports length.
        0
    );

    // Instantiation failed.
    if (wasm_instantiation_result != wasmer_result_t::WASMER_OK) {
        efree(instance);

        RETURN_NULL();
    }

    initialize_wasm_instance(instance);

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) instance, wasm_instance_resource_number);

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
    wasm_instance *instance = (wasm_instance *) emalloc(sizeof(wasm_instance));
    instance->instance = NULL;
    instance->exports = NULL;
    instance->exported_functions = NULL;

    wasmer_result_t wasm_instantiation_result = wasmer_instantiate(
        &instance->instance,
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
        efree(instance);

        RETURN_NULL();
    }

    initialize_wasm_instance(instance);

    // Store in and return the result as a resource.
    zend_resource *resource = zend_register_resource((void *) instance, wasm_instance_resource_number);

    RETURN_RES(resource);
}

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
 *         // with a Wasm value directly
 *         wasm_value(WASM_TYPE_I32, 1),
 *
 *         // with a PHP value, the Wasm type will be infered
 *         2,
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
    wasm_instance *instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));

    if (NULL == instance) {
        RETURN_NULL();
    }

    // There is no export definition.
    if (instance->exported_functions->empty()) {
        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "The instance has no exports, cannot call the function `%.*s`.",
            (int) function_name_length,
            function_name
        );

        return;
    }

    // Look for a function of the given name in the export definitions.
    std::unordered_map<std::string, wasm_exported_function *>::iterator iterator;

    iterator = instance->exported_functions->find(std::string((const char *) function_name, function_name_length));

    // No function with the given name has been found.
    if (iterator == instance->exported_functions->end()) {
        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "The instance has no exported function named `%.*s`.",
            (int) function_name_length,
            function_name
        );

        return;
    }

    const wasm_exported_function *wasm_function = iterator->second;

    {
        // Check the given signature matches the expected signature.
        int32_t number_of_expected_arguments = (int32_t) wasm_function->input_arity;
        int32_t number_of_given_arguments = (int32_t) zend_hash_num_elements(inputs);
        int32_t diff = number_of_expected_arguments - number_of_given_arguments;

        if (diff > 0) {
            zend_throw_exception_ex(
                zend_ce_exception,
                0,
                "Missing %d argument(s) when calling the `%.*s` exported function; Expect %d argument(s), given %d.",
                diff,
                (int) function_name_length,
                function_name,
                number_of_expected_arguments,
                number_of_given_arguments
            );

            return;
        } else if (diff < 0) {
            zend_throw_exception_ex(
                zend_ce_exception,
                0,
                "Given %d extra argument(s) when calling the `%.*s` exported function; Expect %d argument(s), given %d.",
                -diff,
                (int) function_name_length,
                function_name,
                number_of_expected_arguments,
                number_of_given_arguments
            );

            return;
        }
    }

    // Convert the inputs as Wasm values, or extract the inputs values
    // from the `wasm_value` resources.
    size_t function_input_length = wasm_function->input_arity;
    wasmer_value_t *function_inputs = (wasmer_value_t *) emalloc(sizeof(wasmer_value_t) * function_input_length);

    {
        zend_ulong nth = 0;
        zval *value;

        ZEND_HASH_FOREACH_VAL(inputs, value)
            zend_uchar php_type = Z_TYPE_P(value);
            wasmer_value_tag wasm_type = wasm_function->inputs[nth];

            // The value is a resource. We expect it to be a `wasm_value` resource.
            if (php_type == IS_RESOURCE) {
                function_inputs[nth] = *wasm_value_from_resource(Z_RES_P(value));
            }
            // Convert PHP integer to Wasm i32.
            else if (wasm_type == wasmer_value_tag::WASM_I32) {
                if (php_type != IS_LONG) {
                    zend_throw_exception_ex(
                        zend_ce_exception,
                        0,
                        "Argument #%d of `%.*s` must be an `i32` (integer).",
                        (int) nth + 1,
                        (int) function_name_length,
                        function_name
                    );

                    return;
                }

                function_inputs[nth].tag = wasmer_value_tag::WASM_I32;
                function_inputs[nth].value.I32 = (int32_t) value->value.lval;
            }
            // Convert PHP integer to Wasm i64.
            else if (wasm_type == wasmer_value_tag::WASM_I64) {
                if (php_type != IS_LONG) {
                    zend_throw_exception_ex(
                        zend_ce_exception,
                        0,
                        "Argument #%d of `%.*s` must be an `i64` (integer).",
                        (int) nth + 1,
                        (int) function_name_length,
                        function_name
                    );

                    return;
                }

                function_inputs[nth].tag = wasmer_value_tag::WASM_I64;
                function_inputs[nth].value.I64 = (int64_t) value->value.lval;
            }
            // Convert PHP integer to Wasm f32.
            else if (wasm_type == wasmer_value_tag::WASM_F32) {
                if (php_type != IS_DOUBLE) {
                    zend_throw_exception_ex(
                        zend_ce_exception,
                        0,
                        "Argument #%d of `%.*s` must be an `f32` (float).",
                        (int) nth + 1,
                        (int) function_name_length,
                        function_name
                    );

                    return;
                }

                function_inputs[nth].tag = wasmer_value_tag::WASM_F32;
                function_inputs[nth].value.F32 = (float) value->value.dval;
            }
            // Convert PHP integer to Wasm f64.
            else if (wasm_type == wasmer_value_tag::WASM_F64) {
                if (php_type != IS_DOUBLE) {
                    zend_throw_exception_ex(
                        zend_ce_exception,
                        0,
                        "Argument #%d of `%.*s` must be an `f64` (float).",
                        (int) nth + 1,
                        (int) function_name_length,
                        function_name
                    );

                    return;
                }

                function_inputs[nth].tag = wasmer_value_tag::WASM_F64;
                function_inputs[nth].value.F64 = (double) value->value.dval;
            }
            // Unreacheable.
            else {
                zend_throw_exception_ex(
                    zend_ce_exception,
                    0,
                    "Invalid argument type at position #%d when calling the `%.*s` exported function: Only i32, i64, f32, and f64 are supported.",
                    (int) nth + 1,
                    (int) function_name_length,
                    function_name
                );

                return;
            }

            ++nth;
        ZEND_HASH_FOREACH_END();
    }

    // PHP expects at most one output.
    size_t function_output_length = wasm_function->output_arity;
    wasmer_value_t *function_outputs = NULL;

    if (function_output_length > 0) {
        function_output_length = 1;
        function_outputs = (wasmer_value_t *) emalloc(sizeof(wasmer_value_t) * function_output_length);
    }

    // Call the Wasm function.
    wasmer_result_t function_call_result = wasmer_instance_call(
        // Instance.
        instance->instance,
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
        efree(function_outputs);

        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "Failed to call the `%.*s` exported function.",
            (int) function_name_length,
            function_name
        );

        return;
    }

    if (function_output_length > 0) {
        // Read the first output, because PHP expects at most one
        // output, as said above.
        wasmer_value_t function_output = function_outputs[0];

        // Convert the Wasm value to a PHP value.
        if (function_output.tag == wasmer_value_tag::WASM_I32) {
            efree(function_outputs);

            RETURN_LONG(function_output.value.I32);
        } else if (function_output.tag == wasmer_value_tag::WASM_I64) {
            efree(function_outputs);

            RETURN_LONG(function_output.value.I64);
        } else if (function_output.tag == wasmer_value_tag::WASM_F32) {
            efree(function_outputs);

            RETURN_DOUBLE(function_output.value.F32);
        } else if (function_output.tag == wasmer_value_tag::WASM_F64) {
            efree(function_outputs);

            RETURN_DOUBLE(function_output.value.F64);
        } else {
            efree(function_outputs);

            zend_throw_exception_ex(
                zend_ce_exception,
                0,
                "The exported function `%.*s` has returned a value of unknown type.",
                (int) function_name_length,
                function_name
            );

            return;
        }
    }
    // No output, `void` is similar to `null` in PHP.
    else {
        efree(function_outputs);

        RETURN_NULL();
    }
}

/**
 * Declare the parameter information for the
 * `wasm_get_memory_buffer` function.
 */
ZEND_BEGIN_ARG_WITH_RETURN_OBJ_INFO_EX(arginfo_wasm_get_memory_buffer, ZEND_RETURN_REFERENCE, ARITY(1), WasmArrayBuffer, NULLABLE)
    ZEND_ARG_TYPE_INFO(0, wasm_instance, IS_RESOURCE, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `wasm_get_memory_buffer` function.
 *
 * # Usage
 *
 * ```php
 * $bytes = wasm_fetch_bytes('my_program.wasm');
 * $instance = wasm_new_instance($bytes);
 * $memory = wasm_get_memory_buffer($instance);
 * $view = new WasmUint8Array($memory);
 * // enjoy!
 * ```
 */
PHP_FUNCTION(wasm_get_memory_buffer)
{
    zval *wasm_instance_resource;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_RESOURCE(wasm_instance_resource)
    ZEND_PARSE_PARAMETERS_END();

    // Extract the Wasm instance from the resource.
    wasm_instance *instance = wasm_instance_from_resource(Z_RES_P(wasm_instance_resource));

    if (NULL == instance) {
        RETURN_NULL();
    }

    // Read all the export definitions (of all kinds).
    int number_of_exports = wasmer_exports_len(instance->exports);

    // There is no export definition.
    if (number_of_exports == 0) {
        RETURN_NULL();
    }

    // Look for a memory in the export definitions.
    wasmer_memory_t *wasm_memory = NULL;

    for (uint32_t nth = 0; nth < number_of_exports; ++nth) {
        wasmer_export_t *wasm_export = wasmer_exports_get(instance->exports, nth);
        wasmer_import_export_kind wasm_export_kind = wasmer_export_kind(wasm_export);

        // Not a memory definition, let's continue.
        if (wasm_export_kind != wasmer_import_export_kind::WASM_MEMORY) {
            continue;
        }

        // Get the memory instance from the export.
        if (wasmer_export_to_memory(wasm_export, &wasm_memory) == wasmer_result_t::WASMER_OK) {
            break;
        }
    }

    // Gotcha?
    if (wasm_memory == NULL) {
        RETURN_NULL();
    }

    // Get the memory data and its length.
    uint8_t *wasm_memory_data = wasmer_memory_data(wasm_memory);
    uint32_t wasm_memory_data_length = wasmer_memory_data_length(wasm_memory);

    // Create a `WasmArrayBuffer` object.
    zend_object *wasm_array_buffer = create_wasm_array_buffer_object(wasm_array_buffer_class_entry);
    wasm_array_buffer_object *wasm_array_buffer_object = wasm_array_buffer_object_from_zend_object(wasm_array_buffer);

    // Set the internal buffer of `WasmArrayBuffer`.
    wasm_array_buffer_object->memory = wasm_memory;
    wasm_array_buffer_object->buffer = (int8_t *) wasm_memory_data;
    wasm_array_buffer_object->buffer_length = (size_t) wasm_memory_data_length;

    // Do not free the buffer, it's not allocated by PHP.
    wasm_array_buffer_object->allocated_buffer = false;

    // Return the `WasmArrayBuffer` instance.
    ZVAL_OBJ(return_value, &wasm_array_buffer_object->instance);
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
 * $error = wasm_get_last_error();
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
    PHP_FE(wasm_value,									arginfo_wasm_value)
    PHP_FE(wasm_invoke_function,						arginfo_wasm_invoke_function)
    PHP_FE(wasm_get_memory_buffer,						arginfo_wasm_get_memory_buffer)
    PHP_FE(wasm_get_last_error,							arginfo_wasm_get_last_error)
    PHP_FE_END
};

/**
 * Gets the `wasm_typed_array_object` pointer from a `zend_object` pointer.
 */
static inline wasm_typed_array_object *wasm_typed_array_object_from_zend_object(zend_object *object)
{
	return (wasm_typed_array_object *) ((char *)(object) - XtOffsetOf(wasm_typed_array_object, instance));
}

/**
 * Function for a `zend_class_entry` to create one of the `WasmTypedArray` objects.
 */
static zend_object *create_wasm_typed_array_object(zend_class_entry *class_entry)
{
    wasm_typed_array_object *wasm_typed_array = (wasm_typed_array_object *) ecalloc(
        1,
        sizeof(wasm_typed_array_object) + zend_object_properties_size(class_entry)
    );
    wasm_typed_array->wasm_array_buffer = NULL;
    wasm_typed_array->offset = 0;
    wasm_typed_array->length = 0;

    zend_object_std_init(&wasm_typed_array->instance, class_entry);
    object_properties_init(&wasm_typed_array->instance, class_entry);

    {
        zend_class_entry *base_class_entry = class_entry;

        while (base_class_entry->parent) {
            base_class_entry = base_class_entry->parent;
        }

        if (base_class_entry == wasm_typed_array_int8_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::INT8;
        } else if (base_class_entry == wasm_typed_array_uint8_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::UINT8;
        } else if (base_class_entry == wasm_typed_array_int16_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::INT16;
        } else if (base_class_entry == wasm_typed_array_uint16_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::UINT16;
        } else if (base_class_entry == wasm_typed_array_int32_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::INT32;
        } else if (base_class_entry == wasm_typed_array_uint32_class_entry) {
            wasm_typed_array->kind = wasm_typed_array_kind::UINT32;
        } else {
            zend_error(E_ERROR, "WebAssembly buffer view has an unknown type.");
        }
    }

    wasm_typed_array->instance.handlers = &wasm_typed_array_class_entry_handlers;

    return &wasm_typed_array->instance;
}

/**
 * Handler for a `zend_class_entry` to destroy (i.e. call the
 * destructor on the userland) of one of the `WasmTypedArray` objects.
 */
static void destroy_wasm_typed_array_object(zend_object *object)
{
    zend_objects_destroy_object(object);
}

/**
 * Handler for a `zend_class_entry` to free one of the `WasmTypedArray` objects.
 */
static void free_wasm_typed_array_object(zend_object *object)
{
    wasm_typed_array_object *wasm_typed_array_object = wasm_typed_array_object_from_zend_object(object);

    if (wasm_typed_array_object->wasm_array_buffer != NULL) {
        zval_ptr_dtor(wasm_typed_array_object->wasm_array_buffer);
    }

    zend_object_std_dtor(object);
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::__construct` method.
 */
ZEND_BEGIN_ARG_INFO_EX(arginfo_wasmtypedarray___construct, 0, ZEND_RETURN_VALUE, ARITY(1))
    ZEND_ARG_OBJ_INFO(0, wasm_array_buffer, WasmArrayBuffer, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, offset, IS_LONG, NOT_NULLABLE)
    ZEND_ARG_TYPE_INFO(0, length, IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::__construct` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(256);
 * $offset = 1;
 * $length = 7;
 * $uint8 = new WasmUint8Array($buffer, $offset, $length);
 * ```
 */
PHP_FUNCTION(WasmTypedArray___construct)
{
    zval *wasm_array_buffer;
    zend_long offset = 0;
    zend_long length = 0;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 3)
        Z_PARAM_OBJECT_OF_CLASS(wasm_array_buffer, wasm_array_buffer_class_entry);
        Z_PARAM_OPTIONAL
        Z_PARAM_LONG(offset)
        Z_PARAM_LONG(length)
    ZEND_PARSE_PARAMETERS_END();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();
    wasm_array_buffer_object *wasm_array_buffer_object = wasm_array_buffer_object_from_zend_object(Z_OBJ_P(wasm_array_buffer));

    if (offset < 0) {
        zend_throw_exception_ex(zend_ce_exception, 0, "Offset must be non-negative; given %lld.", offset);

        return;
    }

    if (offset > wasm_array_buffer_object->buffer_length) {
        zend_throw_exception_ex(
            zend_ce_exception,
            1,
            "Offset must be smaller than the array buffer length; given %lld, buffer length is %zu.",
            offset,
            wasm_array_buffer_object->buffer_length
        );

        return;
    }

    if (length < 0) {
        zend_throw_exception_ex(zend_ce_exception, 2, "Length must be non-negative; given %lld.", length);

        return;
    }

    // Assign the `WasmArrayBuffer` in the `WasmTypedArray`.
    wasm_typed_array_object->wasm_array_buffer = wasm_array_buffer;
    Z_ADDREF_P(wasm_array_buffer);

    // Assign the offset.
    wasm_typed_array_object->offset = (size_t) offset;

    // Assign the length.
    {
        size_t bytes_per_buffer_item;

        switch (wasm_typed_array_object->kind) {
            case wasm_typed_array_kind::INT8:
            case wasm_typed_array_kind::UINT8:
                bytes_per_buffer_item = 1;

                break;

            case wasm_typed_array_kind::INT16:
            case wasm_typed_array_kind::UINT16:
                bytes_per_buffer_item = 2;

                break;

            case wasm_typed_array_kind::INT32:
            case wasm_typed_array_kind::UINT32:
                bytes_per_buffer_item = 4;

                break;

            default:
                zend_throw_exception(zend_ce_exception, "Invalid WebAssembly typed array type.", 3);

                return;
        }

        size_t maximum_length = (wasm_array_buffer_object->buffer_length - offset) / bytes_per_buffer_item;

        if (length == 0) {
            wasm_typed_array_object->length = maximum_length;
        } else if (length > maximum_length) {
            zend_throw_exception_ex(
                zend_ce_exception,
                4,
                "Length must not be greater than the buffer length; given %lld, maximum length is %zu.",
                length,
                maximum_length
            );

            return;
        } else {
            wasm_typed_array_object->length = (size_t) length;
        }
    }

    // Set the view at the specific offset.
    wasm_typed_array_object->view.as_int8 = wasm_array_buffer_object->buffer;
    wasm_typed_array_object->view.as_int8 += offset;
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::getOffset` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_get_offset, ZEND_RETURN_VALUE, ARITY(0), IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::getOffset` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * assert($view->getOffset() == 3);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_get_offset)
{
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    RETURN_LONG(wasm_typed_array_object->offset);
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::getLength` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_get_length, ZEND_RETURN_VALUE, ARITY(0), IS_LONG, NOT_NULLABLE)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::getLength` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * assert($view->getLength() == 5);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_get_length)
{
    ZEND_PARSE_PARAMETERS_NONE();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    RETURN_LONG(wasm_typed_array_object->length);
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::offsetGet` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_offset_get, ZEND_RETURN_VALUE, ARITY(1), _IS_NUMBER, NOT_NULLABLE)
    ZEND_ARG_INFO(0, offset)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::offsetGet` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * assert($view[1] == 0);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_offset_get)
{
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    if (offset < 0 || offset >= wasm_typed_array_object->length) {
        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "Offset is outside the view range [0; %zu]; given %lld.",
            wasm_typed_array_object->length,
            offset
        );

        return;
    }

    switch (wasm_typed_array_object->kind) {
        case wasm_typed_array_kind::INT8:
            RETURN_LONG(wasm_typed_array_object->view.as_int8[offset]);

            break;

        case wasm_typed_array_kind::UINT8:
            RETURN_LONG(wasm_typed_array_object->view.as_uint8[offset]);

            break;

        case wasm_typed_array_kind::INT16:
            RETURN_LONG(wasm_typed_array_object->view.as_int16[offset]);

            break;

        case wasm_typed_array_kind::UINT16:
            RETURN_LONG(wasm_typed_array_object->view.as_uint16[offset]);

            break;

        case wasm_typed_array_kind::INT32:
            RETURN_LONG(wasm_typed_array_object->view.as_int32[offset]);

            break;

        case wasm_typed_array_kind::UINT32:
            RETURN_LONG(wasm_typed_array_object->view.as_uint32[offset]);

            break;

        default:
            zend_throw_exception(zend_ce_exception, "Invalid WebAssembly typed array type.", 1);

            return;
    }
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::offsetSet` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_offset_set, ZEND_RETURN_VALUE, ARITY(2), IS_VOID, NOT_NULLABLE)
    ZEND_ARG_INFO(0, offset)
    ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::offsetSet` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * $view[0] = 153;
 * assert($view[0] == 153);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_offset_set)
{
    zend_long offset;
    zval *value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
        Z_PARAM_LONG(offset)
        Z_PARAM_ZVAL(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    if (offset < 0 || offset >= wasm_typed_array_object->length) {
        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "Offset is outside the view range [0; %zu]; given %lld.",
            wasm_typed_array_object->length,
            offset
        );

        return;
    }

    convert_to_long_ex(value);

    switch (wasm_typed_array_object->kind) {
        case wasm_typed_array_kind::INT8:
            wasm_typed_array_object->view.as_int8[offset] = Z_LVAL_P(value);

            break;

        case wasm_typed_array_kind::UINT8:
            wasm_typed_array_object->view.as_uint8[offset] = Z_LVAL_P(value);

            break;

        case wasm_typed_array_kind::INT16:
            wasm_typed_array_object->view.as_int16[offset] = Z_LVAL_P(value);

            break;

        case wasm_typed_array_kind::UINT16:
            wasm_typed_array_object->view.as_uint16[offset] = Z_LVAL_P(value);

            break;

        case wasm_typed_array_kind::INT32:
            wasm_typed_array_object->view.as_int32[offset] = Z_LVAL_P(value);

            break;

        case wasm_typed_array_kind::UINT32:
            wasm_typed_array_object->view.as_uint32[offset] = Z_LVAL_P(value);

            break;

        default:
            zend_throw_exception(zend_ce_exception, "Invalid WebAssembly typed array type.", 1);

            return;
    }

    zval_ptr_dtor(value);
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::offsetExists` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_offset_exists, ZEND_RETURN_VALUE, ARITY(1), _IS_BOOL, NOT_NULLABLE)
    ZEND_ARG_INFO(0, offset)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::offsetExists` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * assert($view[0]);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_offset_exists)
{
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    if (offset < 0 || offset >= wasm_typed_array_object->length) {
        RETURN_FALSE;
    } else {
        RETURN_TRUE;
    }
}

/**
 * Declare the parameter information for the
 * `WasmTypedArray::offsetUnset` method.
 */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(arginfo_wasmtypedarray_offset_unset, ZEND_RETURN_VALUE, ARITY(1), IS_VOID, NOT_NULLABLE)
    ZEND_ARG_INFO(0, offset)
ZEND_END_ARG_INFO()

/**
 * Declare the `WasmTypedArray::offsetUnset` method.
 *
 * # Usage
 *
 * ```php
 * $buffer = new WasmArrayBuffer(42);
 * $view = new WasmUint8Array($buffer, 3, 5);
 * $view[0] = 1;
 * unset($view[0]);
 * assert($view[0] == 0);
 * ```
 */
PHP_FUNCTION(WasmTypedArray_offset_unset)
{
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
        Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_typed_array_object *wasm_typed_array_object = WASM_TYPED_ARRAY_OBJECT_THIS();

    if (offset < 0 || offset >= wasm_typed_array_object->length) {
        zend_throw_exception_ex(
            zend_ce_exception,
            0,
            "Offset is outside the view range [0; %zu]; given %lld.",
            wasm_typed_array_object->length,
            offset
        );

        return;
    }

    switch (wasm_typed_array_object->kind) {
        case wasm_typed_array_kind::INT8:
            wasm_typed_array_object->view.as_int8[offset] = 0;

            break;

        case wasm_typed_array_kind::UINT8:
            wasm_typed_array_object->view.as_uint8[offset] = 0;

            break;

        case wasm_typed_array_kind::INT16:
            wasm_typed_array_object->view.as_int16[offset] = 0;

            break;

        case wasm_typed_array_kind::UINT16:
            wasm_typed_array_object->view.as_uint16[offset] = 0;

            break;

        case wasm_typed_array_kind::INT32:
            wasm_typed_array_object->view.as_int32[offset] = 0;

            break;

        case wasm_typed_array_kind::UINT32:
            wasm_typed_array_object->view.as_uint32[offset] = 0;

            break;

        default:
            zend_throw_exception(zend_ce_exception, "Invalid WebAssembly typed array type.", 1);

            return;
    }
}

// Declare the methods of the `WasmTypedArray` classes with their information.
static const zend_function_entry wasm_typed_array_methods[] = {
    PHP_ME_MAPPING(__construct,		WasmTypedArray___construct,		arginfo_wasmtypedarray___construct, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(getOffset,		WasmTypedArray_get_offset,		arginfo_wasmtypedarray_get_offset, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(getLength,		WasmTypedArray_get_length,		arginfo_wasmtypedarray_get_length, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(offsetGet,		WasmTypedArray_offset_get,		arginfo_wasmtypedarray_offset_get, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(offsetSet,		WasmTypedArray_offset_set,		arginfo_wasmtypedarray_offset_set, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(offsetExists,	WasmTypedArray_offset_exists,	arginfo_wasmtypedarray_offset_exists, ZEND_ACC_PUBLIC)
    PHP_ME_MAPPING(offsetUnset,		WasmTypedArray_offset_unset,	arginfo_wasmtypedarray_offset_unset, ZEND_ACC_PUBLIC)
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

    zend_class_entry class_entry;

    // Declare the `WasmArrayBuffer` class.
    INIT_CLASS_ENTRY(class_entry, "WasmArrayBuffer", wasm_array_buffer_methods);
    wasm_array_buffer_class_entry = zend_register_internal_class(&class_entry TSRMLS_CC);
    wasm_array_buffer_class_entry->create_object = create_wasm_array_buffer_object;
    wasm_array_buffer_class_entry->ce_flags |= ZEND_ACC_FINAL | ZEND_ACC_IMMUTABLE;

    memcpy(&wasm_array_buffer_class_entry_handlers, zend_get_std_object_handlers(), sizeof(wasm_array_buffer_class_entry_handlers));
    wasm_array_buffer_class_entry_handlers.offset = XtOffsetOf(wasm_array_buffer_object, instance);
    wasm_array_buffer_class_entry_handlers.dtor_obj = destroy_wasm_array_buffer_object;
    wasm_array_buffer_class_entry_handlers.free_obj = free_wasm_array_buffer_object;
    wasm_array_buffer_class_entry_handlers.clone_obj = NULL;

    // Declare the `WasmTypedArray` classes.
    // All the `WasmTypedArray` classes share the same implementation.
#define DECLARE_WASM_TYPED_ARRAY(class_name, type, bytes_per_element) \
    INIT_CLASS_ENTRY(class_entry, #class_name, wasm_typed_array_methods); \
    wasm_typed_array_##type##_class_entry = zend_register_internal_class(&class_entry TSRMLS_CC); \
    wasm_typed_array_##type##_class_entry->create_object = create_wasm_typed_array_object; \
    wasm_typed_array_##type##_class_entry->ce_flags |= ZEND_ACC_IMMUTABLE; \
    zend_class_implements(wasm_typed_array_##type##_class_entry TSRMLS_CC, 1, zend_ce_arrayaccess); \
	zend_declare_class_constant_long(wasm_typed_array_##type##_class_entry, "BYTES_PER_ELEMENT", sizeof("BYTES_PER_ELEMENT")-1, (zend_long) bytes_per_element);

    DECLARE_WASM_TYPED_ARRAY(WasmInt8Array, int8, 1);
    DECLARE_WASM_TYPED_ARRAY(WasmUint8Array, uint8, 1);
    DECLARE_WASM_TYPED_ARRAY(WasmInt16Array, int16, 2);
    DECLARE_WASM_TYPED_ARRAY(WasmUint16Array, uint16, 2);
    DECLARE_WASM_TYPED_ARRAY(WasmInt32Array, int32, 4);
    DECLARE_WASM_TYPED_ARRAY(WasmUint32Array, uint32, 4);

#undef DECLARE_WASM_TYPED_ARRAY

    memcpy(&wasm_typed_array_class_entry_handlers, zend_get_std_object_handlers(), sizeof(wasm_typed_array_class_entry_handlers));
    wasm_typed_array_class_entry_handlers.offset = XtOffsetOf(wasm_typed_array_object, instance);
    wasm_typed_array_class_entry_handlers.dtor_obj = destroy_wasm_typed_array_object;
    wasm_typed_array_class_entry_handlers.free_obj = free_wasm_typed_array_object;
    wasm_typed_array_class_entry_handlers.clone_obj = NULL;

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
