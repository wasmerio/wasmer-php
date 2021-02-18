#include "php.h"

#define WASMER_IMPORT_RESOURCE(name)\
extern int le_wasm_##name;\
extern const char *le_wasm_##name##_name;\

#define WASMER_FETCH_RESOURCE(name)\
if (zend_fetch_resource_ex(name##_val, le_wasm_##name##_name,\
                        le_wasm_##name) == NULL) {\
    RETURN_THROWS();\
}

#define WASMER_COPY(name)\
PHP_FUNCTION (wasm_##name##_copy) {\
    zval *name##_val;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)\
            Z_PARAM_RESOURCE(name##_val)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    WASMER_FETCH_RESOURCE(name)\
    \
    wasmer_res *wasm_##name = emalloc(sizeof(wasmer_res));\
    wasm_##name->inner.name = wasm_##name##_copy(((wasmer_res*)Z_RES_P(name##_val)->ptr)->inner.name);\
    wasm_##name->owned = true;\
    \
    zend_resource *name##_res = zend_register_resource(wasm_##name, le_wasm_##name);\
    \
    RETURN_RES(name##_res);\
}

#define WASMER_DELETE_RESOURCE(name)\
PHP_FUNCTION (wasm_##name##_delete) {\
    zval *name##_val;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)\
        Z_PARAM_RESOURCE(name##_val)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    if (zend_fetch_resource_ex(name##_val, le_wasm_##name##_name,\
        le_wasm_##name) == NULL) {\
        RETURN_THROWS();\
    }\
    zend_list_close(Z_RES_P(name##_val));\
    \
    RETURN_TRUE;\
}

// TODO(jubianchi): Implement clone (via wasm_vec_##name##_copy)
#define WASMER_DECLARE_VEC(class_name, macro, name)\
extern zend_class_entry *wasm_exception_oob_ce; \
WASMER_DECLARE_VEC_CONSTRUCT(class_name, name, macro)\
WASMER_DECLARE_VEC_COUNT(class_name, macro, name)\
WASMER_DECLARE_VEC_OFFSET_EXISTS(class_name, macro, name)\
WASMER_DECLARE_VEC_OFFSET_GET(class_name, macro, name)\
WASMER_DECLARE_VEC_OFFSET_SET(class_name, macro, name)\
WASMER_DECLARE_VEC_OFFSET_UNSET(class_name)

#define WASMER_DECLARE_VEC_CONSTRUCT(class_name, name, macro) \
PHP_METHOD (Wasm_Vec_##class_name, __construct) {\
    zend_array *name##s_ht;\
    zend_long size;\
    zend_bool is_null = 1;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 0, 1)\
            Z_PARAM_OPTIONAL\
            Z_PARAM_ARRAY_HT_OR_LONG_OR_NULL(name##s_ht, size, is_null)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    wasm_##name##_vec_c *wasm_##name##_vec = WASMER_##macro##_VEC_P(ZEND_THIS);\
    wasm_##name##_vec_t *vec = emalloc(sizeof(wasm_##name##_vec_t));\
    \
    if (is_null) {\
        wasm_##name##_vec_new_empty(vec);\
    } else if(name##s_ht) {\
        int len = zend_hash_num_elements(name##s_ht);\
        \
        wasm_##name##_vec_new_uninitialized(vec, len);\
        \
        zval *tmp;\
        zend_ulong index;\
        \
        ZEND_HASH_REVERSE_FOREACH_NUM_KEY_VAL(name##s_ht, index, tmp) {\
                wasmer_res *name##_res = WASMER_RES_P(tmp);\
                name##_res->owned = false;\
                \
                vec->data[index] = WASMER_RES_INNER(name##_res, name);\
        } ZEND_HASH_FOREACH_END();\
    } else {\
        wasm_##name##_vec_new_uninitialized(vec, size);\
    }\
    \
    wasm_##name##_vec->vec.inner.name = vec;\
    wasm_##name##_vec->vec.owned = true;\
    wasm_##name##_vec->vec.allocated = true;\
}

#define WASMER_DECLARE_VEC_COUNT(class_name, macro, name)\
PHP_METHOD (Wasm_Vec_##class_name, count) {\
    ZEND_PARSE_PARAMETERS_NONE();\
    \
    wasm_##name##_vec_c *wasm_##name##_vec = WASMER_##macro##_VEC_P(ZEND_THIS);\
    \
    RETURN_LONG(wasm_##name##_vec->vec.inner.name->size);\
}

#define WASMER_DECLARE_VEC_OFFSET_EXISTS(class_name, macro, name)\
PHP_METHOD (Wasm_Vec_##class_name, offsetExists) {\
    zend_long offset;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)\
            Z_PARAM_LONG(offset)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    wasm_##name##_vec_c *wasm_##name##_vec = WASMER_##macro##_VEC_P(ZEND_THIS);\
    \
    if(offset >= wasm_##name##_vec->vec.inner.name->size) {\
        RETURN_FALSE;\
    }\
    \
    RETURN_BOOL(offset < wasm_##name##_vec->vec.inner.name->size);\
}

#define WASMER_DECLARE_VEC_OFFSET_GET(class_name, macro, name)\
PHP_METHOD (Wasm_Vec_##class_name, offsetGet) {\
    zend_long offset;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)\
            Z_PARAM_LONG(offset)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    wasm_##name##_vec_c *wasm_##name##_vec = WASMER_##macro##_VEC_P(ZEND_THIS);\
    \
    if(offset >= wasm_##name##_vec->vec.inner.name->size) {\
        zend_throw_exception_ex(wasm_exception_oob_ce, 0, "Wasm\\Vec\\" #class_name "::offsetGet($offset) index out of bounds");\
        \
        return;\
    }\
    \
    if(!wasm_##name##_vec->vec.inner.name->data[offset]) {\
        RETURN_NULL();\
    }\
    \
    wasmer_res *wasm_##name = emalloc(sizeof(wasmer_res));\
    wasm_##name->inner.name = wasm_##name##_vec->vec.inner.name->data[offset];\
    wasm_##name->owned = false;\
    \
    RETURN_RES(zend_register_resource(wasm_##name, le_wasm_##name));\
}

#define WASMER_DECLARE_VEC_OFFSET_SET(class_name, macro, name)\
PHP_METHOD (Wasm_Vec_##class_name, offsetSet) {\
    zend_long offset;\
    zval *name##_val;\
    \
    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)\
            Z_PARAM_LONG(offset)\
            Z_PARAM_RESOURCE(name##_val)\
    ZEND_PARSE_PARAMETERS_END();\
    \
    WASMER_FETCH_RESOURCE(name)\
    \
    wasm_##name##_vec_c *wasm_##name##_vec = WASMER_##macro##_VEC_P(ZEND_THIS);\
    \
    if(offset >= wasm_##name##_vec->vec.inner.name->size) {\
        zend_throw_exception_ex(wasm_exception_oob_ce, 0, "Wasm\\Vec\\" #class_name "::offsetSet($offset) index out of bounds");\
        \
        return;\
    }\
    \
    wasmer_res *name##_res = WASMER_RES_P(name##_val);\
    name##_res->owned = false;\
    \
    wasm_##name##_vec->vec.inner.name->data[offset] = WASMER_RES_INNER(name##_res, name);\
}

#define WASMER_DECLARE_VEC_OFFSET_UNSET(class_name)\
PHP_METHOD (Wasm_Vec_##class_name, offsetUnset) {\
    zend_throw_exception_ex(zend_ce_exception, 0, "Wasm\\Vec\\" #class_name "::offsetUnset($offset) not available");\
}

#define WASMER_DECLARE_OWN(name)\
WASMER_IMPORT_RESOURCE(name)\
WASMER_DELETE_RESOURCE(name)\

#define WASMER_DECLARE_TYPE(class_name, macro, name)\
WASMER_DECLARE_OWN(name)\
WASMER_DECLARE_VEC(class_name, macro, name)\
WASMER_COPY(name)

#define WASMER_HANDLE_ERROR_START \
{\
    int error_length = wasmer_last_error_length();\
    \
    if (error_length > 0) {\
        char buffer[error_length];\
        wasmer_last_error_message(buffer, error_length);\


#define WASMER_HANDLE_ERROR_END(exception) \
        zend_throw_exception_ex(exception, 0, "%s", buffer);\
        \
        return;\
    }\
}

#define WASMER_HANDLE_ERROR(exception) \
WASMER_HANDLE_ERROR_START \
WASMER_HANDLE_ERROR_END(exception)
