#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasm.h"

#include "../macros.h"
#include "../../wasmer.h"

WASMER_DECLARE_TYPE(ExportType, EXPORTTYPE, exporttype)

WASMER_IMPORT_RESOURCE(externtype)

PHP_FUNCTION (wasm_exporttype_new) {
    char *name;
    size_t name_len;
    zval *externtype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_STRING(name, name_len)
            Z_PARAM_RESOURCE(externtype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(externtype)

    wasm_name_t *name_vec = malloc(sizeof(wasm_name_t));
    name_vec->size = name_len;
    name_vec->data = name;

    wasmer_res *externtype_res = WASMER_RES_P(externtype_val);
    externtype_res->owned = false;

    wasmer_res *exporttype = emalloc(sizeof(wasmer_res));
    exporttype->inner.exporttype = wasm_exporttype_new(name_vec, WASMER_RES_INNER(externtype_res, externtype));
    exporttype->owned = true;

    zend_resource *exporttype_res;
    exporttype_res = zend_register_resource(exporttype, le_wasm_exporttype);

    RETURN_RES(exporttype_res);
}

PHP_FUNCTION (wasm_exporttype_name) {
    zval *exporttype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(exporttype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(exporttype)

    const wasm_name_t *name = wasm_exporttype_name(WASMER_RES_P_INNER(exporttype_val, exporttype));

    RETURN_STRINGL(name->data, name->size);
}

PHP_FUNCTION (wasm_exporttype_type) {
    zval *exporttype_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(exporttype_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(exporttype)

    wasmer_res *externtype = emalloc(sizeof(wasmer_res));
    externtype->inner.externtype = wasm_exporttype_type(WASMER_RES_P_INNER(exporttype_val, exporttype));
    externtype->owned = false;

    zend_resource *externtype_res = zend_register_resource(externtype, le_wasm_externtype);

    RETURN_RES(externtype_res);
}
