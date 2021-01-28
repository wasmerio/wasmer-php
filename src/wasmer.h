typedef struct wasmer_res {
    bool owned;
    union wasmer_res_inner {
        wasm_config_t *config;
        wasm_engine_t *engine;
        wasm_store_t *store;

        wasm_exporttype_t *exporttype;
        wasm_functype_t *functype;
        wasm_globaltype_t *globaltype;
        wasm_tabletype_t *tabletype;
        wasm_memorytype_t *memorytype;
        wasm_externtype_t *externtype;
        wasm_importtype_t *importtype;
        wasm_valtype_t *valtype;
        wasm_limits_t limits;
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

WASMER_CE_STRUCT_DECLARE(valtype)
/**
 * Convert a zval* into a wasm_valtype_vec_c*
 */
#define WASMER_VALTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(valtype, zv)

WASMER_CE_STRUCT_DECLARE(globaltype)
/**
 * Convert a zval* into a wasm_globaltype_vec_c*
 */
#define WASMER_GLOBALTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(globaltype, zv)

WASMER_CE_STRUCT_DECLARE(tabletype)
/**
 * Convert a zval* into a wasm_tabletype_vec_c*
 */
#define WASMER_TABLETYPE_VEC_P(zv) WASMER_DECLARE_CE_P(tabletype, zv)

WASMER_CE_STRUCT_DECLARE(memorytype)
/**
 * Convert a zval* into a wasm_memorytype_vec_c*
 */
#define WASMER_MEMORYTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(memorytype, zv)

WASMER_CE_STRUCT_DECLARE(externtype)
/**
 * Convert a zval* into a wasm_externtype_vec_c*
 */
#define WASMER_EXTERNTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(externtype, zv)

WASMER_CE_STRUCT_DECLARE(importtype)
/**
 * Convert a zval* into a wasm_importtype_vec_c*
 */
#define WASMER_IMPORTTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(importtype, zv)

WASMER_CE_STRUCT_DECLARE(exporttype)
/**
 * Convert a zval* into a wasm_exporttype_vec_c*
 */
#define WASMER_EXPORTTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(exporttype, zv)

WASMER_CE_STRUCT_DECLARE(functype)
/**
 * Convert a zval* into a wasm_functype_vec_c*
 */
#define WASMER_FUNCTYPE_VEC_P(zv) WASMER_DECLARE_CE_P(functype, zv)
