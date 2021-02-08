dnl config.m4 for extension Wasmer

PHP_ARG_WITH(wasmer, for Wasmer support,
    AS_HELP_STRING(--with-wasmer, Include Wasmer support))

if test "$PHP_WASMER" != "no"; then
    PKG_CHECK_MODULES([LIBWASMER], [wasmer >= 1.0.2])
    PKG_CHECK_MODULES([LIBWASMER], [wasmer < 2.0.0])
    PHP_EVAL_INCLINE($LIBWASMER_CFLAGS)
    PHP_EVAL_LIBLINE($LIBWASMER_LIBS, WASMER_SHARED_LIBADD)

    PHP_SUBST(WASMER_SHARED_LIBADD)

    WASMER_API="src/api/config.c src/api/engine.c src/api/store.c src/api/wasmer.c src/api/wat.c"
    WASMER_API_OBJECTS="src/api/objects/extern.c src/api/objects/foreign.c src/api/objects/func.c src/api/objects/frame.c src/api/objects/global.c src/api/objects/instance.c src/api/objects/memory.c src/api/objects/module.c src/api/objects/table.c src/api/objects/trap.c src/api/objects/val.c"
    WASMER_API_TYPES="src/api/types/exporttype.c src/api/types/externtype.c src/api/types/functype.c src/api/types/globaltype.c src/api/types/importtype.c src/api/types/limits.c src/api/types/memorytype.c src/api/types/tabletype.c src/api/types/valkind.c src/api/types/valtype.c"
    WASMER_SOURCES="src/wasmer.c"
    WASMER_ALL_SOURCES="${WASMER_API} ${WASMER_API_OBJECTS} ${WASMER_API_TYPES} ${WASMER_SOURCES}"

    PHP_NEW_EXTENSION(wasmer, $WASMER_ALL_SOURCES, $ext_shared,, $WASMER_CFLAGS)

    PHP_ADD_MAKEFILE_FRAGMENT
fi
