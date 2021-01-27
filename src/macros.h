#define WASMER_RESOURCE_LE(name)\
int le_wasm_##name;\
const char *le_wasm_##name##_name = "wasm_"#name"_t";

#define WASMER_RESOURCE_DTOR(name)\
static ZEND_RSRC_DTOR_FUNC(wasm_##name##_dtor) { \
    wasmer_res *name##_res = (wasmer_res*)res->ptr;\
    wasm_##name##_t *wasm_##name = name##_res->inner.name;\
    \
    if (name##_res->owned) {\
        wasm_##name##_delete(wasm_##name);\
    }\
    \
    efree(res->ptr);\
}

#define WASMER_RESOURCE_DECLARE(name)\
WASMER_RESOURCE_LE(name)\
WASMER_RESOURCE_DTOR(name)

#define WASMER_RESOURCE_DECLARE_WITHOUT_DTOR(name) WASMER_RESOURCE_LE(name)

#define WASMER_RESOURCE_REGISTER(name)\
le_wasm_##name = zend_register_list_destructors_ex(\
    wasm_##name##_dtor,\
    NULL,\
    le_wasm_##name##_name,\
    module_number\
);\
\
if (le_wasm_##name == FAILURE) {\
    return FAILURE;\
}

#define WASMER_VEC_CLASS_REGISTER(class_name, name)\
INIT_NS_CLASS_ENTRY(ce, "Wasm\\Vec", #class_name, class_Wasm_Vec_##class_name##_methods)\
wasm_vec_##name##_ce = zend_register_internal_class(&ce);\
wasm_vec_##name##_ce->ce_flags |= ZEND_ACC_FINAL;\
wasm_vec_##name##_ce->create_object = wasm_##name##_vec_create;\
\
memcpy(&wasm_##name##_vec_object_handlers, &std_object_handlers, sizeof(zend_object_handlers));\
wasm_##name##_vec_object_handlers.offset = XtOffsetOf(struct wasm_##name##_vec_c, std);\
\
zend_class_implements(wasm_vec_##name##_ce, 1, zend_ce_countable);\
zend_class_implements(wasm_vec_##name##_ce, 1, zend_ce_arrayaccess);

#define WASMER_VEC_CLASS_ENTRY_DECLARE(name)\
zend_class_entry *wasm_vec_##name##_ce;\
static zend_object_handlers wasm_##name##_vec_object_handlers;\
zend_object *wasm_##name##_vec_create(zend_class_entry *ce)\
{\
    wasm_##name##_vec_c *wasm_##name##_vec = zend_object_alloc(sizeof(wasm_##name##_vec_c), ce);\
    \
    zend_object_std_init(&wasm_##name##_vec->std, ce);\
    wasm_##name##_vec->std.handlers = &wasm_##name##_vec_object_handlers;\
    \
    return &wasm_##name##_vec->std;\
}
