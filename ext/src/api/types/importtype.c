#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_TYPE(ImportType, IMPORTTYPE, importtype)

WASMER_IMPORT_RESOURCE(externtype)

PHP_FUNCTION (wasm_importtype_new) {
    char *module;
    size_t module_len;
    char *name;
    size_t name_len;
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 3, 3)
            Z_PARAM_STRING(module, module_len)
            Z_PARAM_STRING(name, name_len)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasm_name_t *module_vec = malloc(sizeof(wasm_name_t));
    module_vec->size = module_len;
    module_vec->data = module;

    wasm_name_t *name_vec = malloc(sizeof(wasm_name_t));
    name_vec->size = name_len;
    name_vec->data = name;

    wasmer_res *externtype_res = WASMER_RES_P(externtype_val);
    externtype_res->owned = false;

    wasmer_res *importtype = emalloc(sizeof(wasmer_res));
    importtype->inner.importtype = wasm_importtype_new(module_vec, name_vec, WASMER_RES_INNER(externtype_res, externtype));
    importtype->owned = true;

    zend_resource *importtype_res = zend_register_resource(importtype, le_wasm_importtype);

    RETURN_RES(importtype_res);
}

PHP_FUNCTION (wasm_importtype_module) {
    zval *importtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(importtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(importtype)

    const wasm_name_t *module = wasm_importtype_module(WASMER_RES_P_INNER(importtype_val, importtype));

    RETURN_STRINGL(module->data, module->size);
}

PHP_FUNCTION (wasm_importtype_name) {
    zval *importtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(importtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(importtype)

    const wasm_name_t *name = wasm_importtype_name(WASMER_RES_P_INNER(importtype_val, importtype));

    RETURN_STRINGL(name->data, name->size);
}

PHP_FUNCTION (wasm_importtype_type) {
    zval *importtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(importtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(importtype)

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_importtype_type(WASMER_RES_P_INNER(importtype_val, importtype));
    externtype->owned = false;

    zend_resource *externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
