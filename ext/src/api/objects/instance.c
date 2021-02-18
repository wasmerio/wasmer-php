#include "php.h"
#include "Zend/zend_exceptions.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_OWN(instance)
WASMER_COPY(instance)

WASMER_IMPORT_RESOURCE(store)
WASMER_IMPORT_RESOURCE(module)

extern zend_class_entry *wasm_vec_extern_ce;
extern zend_class_entry *wasm_exception_instantiation_ce;

PHP_FUNCTION (wasm_instance_new) {
    zval *store_val;
    zval *module_val;
    zval *externs_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 3, 3)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_RESOURCE(module_val)
            Z_PARAM_OBJECT(externs_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)
    WASMER_FETCH_RESOURCE(module)

    wasm_store_t *store = WASMER_RES_P_INNER(store_val, store);
    wasm_module_t *module = WASMER_RES_P_INNER(module_val, module);
    wasm_extern_vec_c *externs = WASMER_EXTERN_VEC_P(externs_val);

    wasm_trap_t *trap;
    memset(&trap, 0, sizeof(wasm_trap_t*));
    wasm_instance_t *wasm_instance = wasm_instance_new(store, module, externs->vec.inner.xtern, &trap);

    WASMER_HANDLE_ERROR(wasm_exception_instantiation_ce)

    if (trap != NULL) {
        wasm_byte_vec_t *message_vec = emalloc(sizeof(wasm_byte_vec_t));
        wasm_trap_message(trap, message_vec);

        zend_throw_exception_ex(zend_ce_exception, 0, "%s", message_vec->data);

        efree(message_vec);

        return;
    }

    wasmer_res *instance = emalloc(sizeof(wasmer_res));
    instance->inner.instance = wasm_instance;
    instance->owned = true;

    zend_resource *instance_res = zend_register_resource(instance, le_wasm_instance);

    RETURN_RES(instance_res);
}

PHP_FUNCTION (wasm_instance_exports) {
    zval *instance_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(instance_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(instance)

    wasm_instance_t *instance = WASMER_RES_P_INNER(instance_val, instance);

    wasm_extern_vec_t *externs = emalloc(sizeof(wasm_extern_vec_t));
    wasm_instance_exports(instance, externs);

    zval obj;
    object_init_ex(&obj, wasm_vec_extern_ce);
    wasm_extern_vec_c *ce = WASMER_EXTERN_VEC_P(&obj);
    ce->vec.inner.xtern = externs;
    ce->vec.owned = true;

    RETURN_OBJ(Z_OBJ(obj));
}
