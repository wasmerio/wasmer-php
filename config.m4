dnl config.m4 for extension Wasmer

PHP_ARG_ENABLE(wasmer, whether to enable Wasmer support,
    AS_HELP_STRING(--enable-wasmer, Enable Wasmer support))

if test "$PHP_WASMER" != "no"; then
    AC_DEFINE(HAVE_WASMER, 1, [ Have Wasmer support ])

    PHP_SUBST(WASMER_SHARED_LIBADD)
    PHP_ADD_LIBRARY_WITH_PATH(wasmer, ./lib, WASMER_SHARED_LIBADD)

    WASMER_API="src/api/config.c src/api/engine.c src/api/store.c src/api/wasmer.c src/api/wat.c"
    WASMER_API_OBJECTS="src/api/objects/extern.c src/api/objects/foreign.c src/api/objects/func.c src/api/objects/frame.c src/api/objects/global.c src/api/objects/instance.c src/api/objects/memory.c src/api/objects/module.c src/api/objects/table.c src/api/objects/trap.c src/api/objects/val.c"
    WASMER_API_TYPES="src/api/types/exporttype.c src/api/types/externtype.c src/api/types/functype.c src/api/types/globaltype.c src/api/types/importtype.c src/api/types/limits.c src/api/types/memorytype.c src/api/types/tabletype.c src/api/types/valkind.c src/api/types/valtype.c"
    WASMER_SOURCES="src/wasmer.c"
    WASMER_ALL_SOURCES="${WASMER_API} ${WASMER_API_OBJECTS} ${WASMER_API_TYPES} ${WASMER_SOURCES}"

    PHP_NEW_EXTENSION(wasmer, $WASMER_ALL_SOURCES, $ext_shared,, $WASMER_CFLAGS)

    PHP_ADD_MAKEFILE_FRAGMENT
fi
