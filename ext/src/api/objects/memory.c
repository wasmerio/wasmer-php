#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasm.h"

extern zend_class_entry *wasm_memory_view_ce;
extern zend_object *wasm_memory_view_create(zend_class_entry *ce);
extern zend_class_entry *wasm_memory_ptr_ce;
extern zend_object *wasm_memory_ptr_create(zend_class_entry *ce);

WASMER_DECLARE_OWN(memory)
WASMER_COPY(memory)

WASMER_IMPORT_RESOURCE(store)
WASMER_IMPORT_RESOURCE(memorytype)
WASMER_IMPORT_RESOURCE(extern)

PHP_FUNCTION (wasm_memory_new) {
    zval *store_val;
    zval *memorytype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_RESOURCE(memorytype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)
    WASMER_FETCH_RESOURCE(memorytype)

    wasmer_res *wasm_memory = emalloc(sizeof(wasmer_res));
    wasm_memory->inner.memory = wasm_memory_new(
            WASMER_RES_P_INNER(store_val, store),
            WASMER_RES_P_INNER(memorytype_val, memorytype)
    );
    wasm_memory->owned = true;

    zend_resource *memory_res = zend_register_resource(wasm_memory, le_wasm_memory);

    RETURN_RES(memory_res);
}

PHP_FUNCTION (wasm_memory_type) {
    zval *memory_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memory_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory);

    wasmer_res *wasm_memorytype = emalloc(sizeof(wasmer_res));
    wasm_memorytype->inner.memorytype = wasm_memory_type(WASMER_RES_P_INNER(memory_val, memory));
    wasm_memorytype->owned = true;

    zend_resource *val_res = zend_register_resource(wasm_memorytype, le_wasm_memorytype);

    RETURN_RES(val_res);
}

PHP_FUNCTION (wasm_memory_data) {
    zval *memory_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memory_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory);

    zend_object *wasm_memory_view_obj = wasm_memory_view_create(wasm_memory_view_ce);
    wasm_memory_view_c *wasm_memory_view = (wasm_memory_view_c *) ((char *)(wasm_memory_view_obj) - XtOffsetOf(wasm_memory_view_c, std));
    wasm_memory_view->data = wasm_memory_data(WASMER_RES_P(memory_val)->inner.memory);

    RETURN_OBJ(&wasm_memory_view->std);
}

PHP_FUNCTION (wasm_memory_data_size) {
    zval *memory_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memory_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory);

    size_t size = wasm_memory_data_size(WASMER_RES_P_INNER(memory_val, memory));

    RETURN_LONG(size);
}

PHP_FUNCTION (wasm_memory_size) {
    zval *memory_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memory_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory);

    size_t size = wasm_memory_size(WASMER_RES_P_INNER(memory_val, memory));

    RETURN_LONG(size);
}

PHP_FUNCTION (wasm_memory_grow) {
    zval *memory_val;
    zend_long delta;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(memory_val)
            Z_PARAM_LONG(delta)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory);

    bool success = wasm_memory_grow(WASMER_RES_P_INNER(memory_val, memory), delta);

    RETURN_BOOL(success);
}

PHP_FUNCTION (wasm_memory_same) {
    zval *left_val;
    zval *right_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(left_val)
            Z_PARAM_RESOURCE(right_val)
    ZEND_PARSE_PARAMETERS_END();

    if (zend_fetch_resource_ex(left_val, le_wasm_memory_name, le_wasm_memory) == NULL) {
        RETURN_THROWS();
    }

    if (zend_fetch_resource_ex(right_val, le_wasm_memory_name, le_wasm_memory) == NULL) {
        RETURN_THROWS();
    }

    RETURN_BOOL(wasm_memory_same(
            ((wasmer_res*)Z_RES_P(left_val)->ptr)->inner.memory,
            ((wasmer_res*)Z_RES_P(right_val)->ptr)->inner.memory
    ));
}

PHP_FUNCTION (wasm_memory_as_extern) {
    zval *memory_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(memory_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(memory)

    wasmer_res *wasm_extern = emalloc(sizeof(wasmer_res));
    wasm_extern->inner.xtern = wasm_memory_as_extern(WASMER_RES_P_INNER(memory_val, memory));
    wasm_extern->owned = true;

    RETURN_RES(zend_register_resource(wasm_extern, le_wasm_extern));
}

PHP_METHOD (Wasm_MemoryView, __construct) {

}

PHP_METHOD (Wasm_MemoryView, getI32) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);
    int32_t *result = malloc(sizeof(int32_t));

    memcpy(result, &wasm_memory_view->data[offset], sizeof(*result));

    RETURN_LONG(*result);
}

PHP_METHOD (Wasm_MemoryView, setI32) {
    zend_long offset;
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_LONG(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);

    memcpy(&wasm_memory_view->data[offset], &value, sizeof(int32_t));
}

PHP_METHOD (Wasm_MemoryView, getI64) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);
    int64_t *result = malloc(sizeof(int64_t));

    memcpy(result, &wasm_memory_view->data[offset], sizeof(*result));

    RETURN_LONG(*result);
}

PHP_METHOD (Wasm_MemoryView, setI64) {
    zend_long offset;
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_LONG(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);

    memcpy(&wasm_memory_view->data[offset], &value, sizeof(int64_t));
}

PHP_METHOD (Wasm_MemoryView, getF32) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);
    float32_t *result = malloc(sizeof(float32_t));

    memcpy(result, &wasm_memory_view->data[offset], sizeof(*result));

    RETURN_DOUBLE(*result);
}

PHP_METHOD (Wasm_MemoryView, setF32) {
    zend_long offset;
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_DOUBLE(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);

    memcpy(&wasm_memory_view->data[offset], &value, sizeof(float32_t));
}

PHP_METHOD (Wasm_MemoryView, getF64) {
    zend_long offset;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_LONG(offset)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);
    float64_t *result = malloc(sizeof(float64_t));

    memcpy(result, &wasm_memory_view->data[offset], sizeof(*result));

    RETURN_DOUBLE(*result);
}

PHP_METHOD (Wasm_MemoryView, setF64) {
    zend_long offset;
    zend_long value;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_LONG(offset)
            Z_PARAM_DOUBLE(value)
    ZEND_PARSE_PARAMETERS_END();

    wasm_memory_view_c *wasm_memory_view = WASMER_MEMORY_VIEW_P(ZEND_THIS);

    memcpy(&wasm_memory_view->data[offset], &value, sizeof(float64_t));
}
