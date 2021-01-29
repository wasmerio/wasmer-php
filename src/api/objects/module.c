#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_OWN(module)
WASMER_COPY(module)

WASMER_IMPORT_RESOURCE(store)

extern zend_class_entry *wasm_vec_importtype_ce;
extern zend_class_entry *wasm_vec_exporttype_ce;

PHP_FUNCTION (wasm_module_new) {
    zval *store_val;
    char *wasm;
    size_t wasm_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_STRING(wasm, wasm_len)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)

    wasm_byte_vec_t *wasm_vec = emalloc(sizeof(wasm_byte_vec_t));;
    wasm_vec->size = wasm_len;
    wasm_vec->data = wasm;

    wasmer_res *module = emalloc(sizeof(wasmer_res));
    module->inner.module = wasm_module_new(WASMER_RES_P_INNER(store_val, store), wasm_vec);
    module->owned = true;

    WASMER_HANDLE_ERROR_START
            efree(wasm_vec);
            efree(module);
    WASMER_HANDLE_ERROR_END

    zend_resource *module_res = zend_register_resource(module, le_wasm_module);

    efree(wasm_vec);

    RETURN_RES(module_res);
}

PHP_FUNCTION (wasm_module_validate) {
    zval *store_val;
    char *wasm;
    size_t wasm_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_STRING(wasm, wasm_len)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)

    wasm_byte_vec_t *wasm_vec = malloc(sizeof(wasm_byte_vec_t));;
    wasm_vec->size = wasm_len;
    wasm_vec->data = wasm;

    bool valid = wasm_module_validate(WASMER_RES_P_INNER(store_val, store), wasm_vec);

    WASMER_HANDLE_ERROR_START
            efree(wasm_vec);
    WASMER_HANDLE_ERROR_END

    RETURN_BOOL(valid);
}

PHP_FUNCTION (wasm_module_imports) {
    zval *module_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(module_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(module)

    wasm_importtype_vec_t *importtypes = emalloc(sizeof(wasm_exporttype_vec_t));
    wasm_module_imports(WASMER_RES_P_INNER(module_val, module), importtypes);

    zval obj;
    object_init_ex(&obj, wasm_vec_importtype_ce);
    wasm_importtype_vec_c *ce = WASMER_IMPORTTYPE_VEC_P(&obj);
    ce->vec.inner.importtype = importtypes;
    ce->vec.owned = true;

    RETURN_OBJ(Z_OBJ(obj));
}

PHP_FUNCTION (wasm_module_exports) {
    zval *module_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(module_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(module)

    wasm_exporttype_vec_t *exporttypes = emalloc(sizeof(wasm_exporttype_vec_t));
    wasm_module_exports(WASMER_RES_P_INNER(module_val, module), exporttypes);

    zval obj;
    object_init_ex(&obj, wasm_vec_exporttype_ce);
    wasm_exporttype_vec_c *ce = WASMER_EXPORTTYPE_VEC_P(&obj);
    ce->vec.inner.exporttype = exporttypes;
    ce->vec.owned = true;

    RETURN_OBJ(Z_OBJ(obj));
}

PHP_FUNCTION (wasm_module_serialize) {
    zval *module_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(module_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(module)

    wasm_byte_vec_t *byte_vec = emalloc(sizeof(wasm_byte_vec_t));
    wasm_module_serialize(WASMER_RES_P_INNER(module_val, module), byte_vec);

    char *name = byte_vec->data;
    int length = byte_vec->size;

    efree(byte_vec);

    RETURN_STRINGL(name, length);
}

PHP_FUNCTION (wasm_module_deserialize) {
    zval *store_val;
    char *wasm;
    size_t wasm_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_STRING(wasm, wasm_len)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)

    wasm_byte_vec_t *wasm_vec = emalloc(sizeof(wasm_byte_vec_t));;
    wasm_vec->size = wasm_len;
    wasm_vec->data = wasm;

    wasmer_res *module = emalloc(sizeof(wasmer_res));
    module->inner.module = wasm_module_deserialize(WASMER_RES_P_INNER(store_val, store), wasm_vec);
    module->owned = true;

    WASMER_HANDLE_ERROR_START
            efree(wasm_vec);
    WASMER_HANDLE_ERROR_END

    zend_resource *module_res = zend_register_resource(module, le_wasm_module);

    efree(wasm_vec);

    RETURN_RES(module_res);
}

PHP_FUNCTION (wasm_module_name) {
    zval *module_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(module_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(module)

    wasm_byte_vec_t *name_vec = emalloc(sizeof(wasm_byte_vec_t));
    wasm_module_name(WASMER_RES_P_INNER(module_val, module), name_vec);

    char *name = name_vec->data;
    int length = name_vec->size;

    efree(name_vec);

    RETURN_STRINGL(name, length);
}

PHP_FUNCTION (wasm_module_set_name) {
    zval *module_val;
    char *name;
    size_t name_len;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(module_val)
            Z_PARAM_STRING(name, name_len)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(module)

    wasm_byte_vec_t *name_vec = malloc(sizeof(wasm_byte_vec_t));
    name_vec->size = name_len;
    name_vec->data = name;

    bool result = wasm_module_set_name(WASMER_RES_P_INNER(module_val, module), name_vec);

    RETURN_BOOL(result);
}
