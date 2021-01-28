typedef struct wasmer_res {
    bool owned;
    union wasmer_res_inner {
        wasm_config_t *config;
        wasm_engine_t *engine;
    } inner;
} wasmer_res;

/**
 * Convert a zval* into a wasmer_res*
 */
#define WASMER_RES_P(name) ((wasmer_res*)Z_RES_P(name)->ptr)
/**
 * Convert a zval* into a wasmer_res* and returns the inner type
 */
#define WASMER_RES_P_INNER(name, type) WASMER_RES_P(name)->inner.type

/**
 * Returns the inner type from a wasmer_res*
 */
#define WASMER_RES_INNER(name, type) name->inner.type

#define WASMER_CE_STRUCT_DECLARE(name)\
typedef struct wasm_##name##_vec_c {\
    wasm_##name##_vec_t vec;\
    zend_object std;\
} wasm_##name##_vec_c;

#define WASMER_DECLARE_CE_P(name, zv) ((wasm_##name##_vec_c*)((char*)(Z_OBJ_P(zv)) - XtOffsetOf(wasm_##name##_vec_c, std)))
