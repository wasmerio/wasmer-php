typedef struct wasmer_res {
    bool owned;
    union {
        wasm_config_t *config;
        wasm_engine_t *engine;
        wasm_store_t *store;

        wasm_exporttype_t *exporttype;
        wasm_externtype_t *externtype;
        wasm_functype_t *functype;
        wasm_globaltype_t *globaltype;
        wasm_importtype_t *importtype;
        wasm_limits_t limits;
        wasm_memorytype_t *memorytype;
        wasm_tabletype_t *tabletype;
        wasm_valtype_t *valtype;

        wasm_global_t *global;
        wasm_instance_t *instance;
        wasm_foreign_t *foreign;
        wasm_frame_t *frame;
        wasm_func_t *func;
        wasm_memory_t *memory;
        wasm_module_t *module;
        wasm_table_t *table;
        wasm_trap_t *trap;
        wasm_val_t val;
        wasm_extern_t *xtern;
    } inner;
} wasmer_res;

typedef struct wasmer_vec {
    bool owned;
    bool allocated;
    union {
        wasm_exporttype_vec_t *exporttype;
        wasm_externtype_vec_t *externtype;
        wasm_functype_vec_t *functype;
        wasm_globaltype_vec_t *globaltype;
        wasm_importtype_vec_t *importtype;
        wasm_memorytype_vec_t *memorytype;
        wasm_tabletype_vec_t *tabletype;
        wasm_valtype_vec_t *valtype;

        wasm_frame_vec_t *frame;
        wasm_val_vec_t *val;
        wasm_extern_vec_t *xtern;
    } inner;
} wasmer_vec;

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
    wasmer_vec vec;\
    zend_object std;\
} wasm_##name##_vec_c;

#define WASMER_DECLARE_CE_P(name, zv) (wasm_##name##_vec_c*)((char*)(Z_OBJ_P(zv)) - Z_OBJ_P(zv)->handlers->offset)

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

WASMER_CE_STRUCT_DECLARE(extern)
/**
 * Convert a zval* into a wasm_extern_vec_c*
 */
#define WASMER_EXTERN_VEC_P(zv) WASMER_DECLARE_CE_P(extern, zv)

WASMER_CE_STRUCT_DECLARE(frame)
/**
 * Convert a zval* into a wasm_frame_vec_c*
 */
#define WASMER_FRAME_VEC_P(zv) WASMER_DECLARE_CE_P(frame, zv)

WASMER_CE_STRUCT_DECLARE(val)
/**
 * Convert a zval* into a wasm_val_vec_c*
 */
#define WASMER_VAL_VEC_P(zv) WASMER_DECLARE_CE_P(val, zv)
