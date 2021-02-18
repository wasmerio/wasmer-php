#include "php.h"
#include "Zend/zend_exceptions.h"
#include "Zend/zend_closures.h"

#include "wasmer_wasm.h"

#include "../macros.h"
#include "../../wasm.h"

WASMER_DECLARE_OWN(func)

extern zend_class_entry *wasm_vec_val_ce;

WASMER_IMPORT_RESOURCE(functype)
WASMER_IMPORT_RESOURCE(store)
WASMER_IMPORT_RESOURCE(extern)

wasm_trap_t *func_trampoline(void *env, const wasm_val_vec_t *args, wasm_val_vec_t *results) {
    wasmer_func_env *fenv = (wasmer_func_env*) env;
    zval retval;

    zend_fcall_info fci;
    fci.size = sizeof(zend_fcall_info);
    fci.retval = &retval;
    fci.params = emalloc(sizeof(zval) * args->size);
    fci.param_count = args->size;
    fci.named_params = NULL;

    for (int i = 0; i < args->size; i++) {
        wasm_val_t val = args->data[i];

        switch (val.kind) {
            case WASM_I32:
                ZVAL_LONG(&fci.params[i], val.of.i32);
                break;

            case WASM_I64:
                ZVAL_LONG(&fci.params[i], val.of.i64);
                break;

            case WASM_F32:
                ZVAL_DOUBLE(&fci.params[i], val.of.f32);
                break;

            case WASM_F64:
                ZVAL_DOUBLE(&fci.params[i], val.of.f64);
                break;

            // TODO(jubianchi): Add default case (for anyref and funcref)
        }
    }

    ZVAL_UNDEF(&retval);
    zend_call_function(&fci, &fenv->fcc);
    int type = Z_TYPE(retval);

    wasm_val_t *val = emalloc(sizeof(wasm_val_t));

    switch (type) {
        case IS_LONG:
            val->kind = WASM_I32;
            val->of.i32 = zval_get_long(&retval);
            results->data[0] = *val;
            break;

        case IS_DOUBLE:
            val->kind = WASM_F32;
            val->of.f32 = 13;
            results->data[0] = *val;
            break;

        case IS_ARRAY:
            // TODO(jubianchi): Implement array return (multi value)
            break;

        // TODO(jubianchi): Add default case
    }

    efree(fci.params);
    efree(val);

    return NULL;
}

void func_env_finalizer(void *env) {
    wasmer_func_env *fenv = (wasmer_func_env*) env;

    if (fenv->fcc.function_handler->common.fn_flags & ZEND_ACC_CLOSURE) {
        OBJ_RELEASE(ZEND_CLOSURE_OBJECT(fenv->fcc.function_handler));
    }

    efree(env);
}

PHP_FUNCTION (wasm_func_new) {
    zval *store_val;
    zval *functype_val;
    zend_fcall_info fci;
    // TODO: Env finalizer has some problem, we have to use malloc instead of emalloc. Fix that.
    wasmer_func_env *env = emalloc(sizeof(wasmer_func_env));

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 3, 3)
            Z_PARAM_RESOURCE(store_val)
            Z_PARAM_RESOURCE(functype_val)
            Z_PARAM_FUNC(fci, env->fcc)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(store)
    WASMER_FETCH_RESOURCE(functype)

    wasmer_res *func = emalloc(sizeof(wasmer_res));
    func->inner.func = wasm_func_new_with_env(
            WASMER_RES_P_INNER(store_val, store),
            WASMER_RES_P_INNER(functype_val, functype),
            func_trampoline,
            env,
            func_env_finalizer
    );
    func->owned = true;

    if (!func->inner.func) {
        zend_throw_exception_ex(zend_ce_exception, 0, "%s", "Failed to create function");

        return;
    }

    zend_resource *func_res = zend_register_resource(func, le_wasm_func);

    if (env->fcc.function_handler->common.fn_flags & ZEND_ACC_CLOSURE) {
        GC_ADDREF(ZEND_CLOSURE_OBJECT(env->fcc.function_handler));
    }

    RETURN_RES(func_res);
}

PHP_FUNCTION (wasm_func_new_with_env) {
    ZEND_PARSE_PARAMETERS_NONE();

    // TODO(jubianchi): Implement
    zend_throw_error(NULL, "Not yet implemented");
}

PHP_FUNCTION (wasm_func_type) {
    zval *func_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(func_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(func)

    wasmer_res *functype = emalloc(sizeof(wasmer_res));
    functype->inner.functype = wasm_func_type(WASMER_RES_P_INNER(func_val, func));
    functype->owned = false;

    RETURN_RES(zend_register_resource(functype, le_wasm_functype));
}

PHP_FUNCTION (wasm_func_param_arity) {
    zval *func_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(func_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(func)

    size_t arity = wasm_func_param_arity(WASMER_RES_P_INNER(func_val, func));

    RETURN_LONG(arity);
}

PHP_FUNCTION (wasm_func_result_arity) {
    zval *func_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(func_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(func)

    size_t arity = wasm_func_result_arity(WASMER_RES_P_INNER(func_val, func));

    RETURN_LONG(arity);
}

PHP_FUNCTION (wasm_func_call) {
    zval *func_val;
    zval *args_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 2, 2)
            Z_PARAM_RESOURCE(func_val)
            Z_PARAM_OBJECT(args_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(func)

    wasm_func_t *func = WASMER_RES_P_INNER(func_val, func);
    wasm_val_vec_c *args = WASMER_VAL_VEC_P(args_val);

    // TODO(jubianchi): Throw if args size is different from wasm_func_param_arity

    wasm_val_vec_t *results = emalloc(sizeof(wasm_val_vec_t));
    wasm_val_vec_new_uninitialized(results, wasm_func_result_arity(func));

    wasm_trap_t *trap = wasm_func_call(func, args->vec.inner.val, results);

    if (trap != NULL) {
        wasm_byte_vec_t *message_vec = emalloc(sizeof(wasm_byte_vec_t));
        wasm_trap_message(trap, message_vec);

        zend_throw_exception_ex(zend_ce_exception, 0, "%s", message_vec->data);

        efree(message_vec);
        efree(results);

        return;
    }

    zval obj;
    object_init_ex(&obj, wasm_vec_val_ce);
    wasm_val_vec_c *ce = WASMER_VAL_VEC_P(&obj);
    ce->vec.inner.val = results;
    ce->vec.owned = false;
    ce->vec.allocated = true;

    RETURN_OBJ(Z_OBJ(obj));
}

PHP_FUNCTION (wasm_func_as_extern) {
    zval *func_val;

    ZEND_PARSE_PARAMETERS_START_EX(ZEND_PARSE_PARAMS_THROW, 1, 1)
            Z_PARAM_RESOURCE(func_val)
    ZEND_PARSE_PARAMETERS_END();

    WASMER_FETCH_RESOURCE(func)

    wasmer_res *wasm_extern = emalloc(sizeof(wasmer_res));
    wasm_extern->inner.xtern = wasm_func_as_extern(WASMER_RES_P_INNER(func_val, func));
    wasm_extern->owned = true;

    RETURN_RES(zend_register_resource(wasm_extern, le_wasm_extern));
}
