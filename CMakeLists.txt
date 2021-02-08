cmake_minimum_required(VERSION 3.17)
project(wasmer-php C)

add_compile_definitions(HAVE_WASMER)

set(WASMER_API ext/src/api/config.c ext/src/api/engine.c ext/src/api/store.c ext/src/api/wasmer.c ext/src/api/wat.c)
set(WASMER_API_OBJECTS ext/src/api/objects/extern.c ext/src/api/objects/foreign.c ext/src/api/objects/func.c ext/src/api/objects/frame.c ext/src/api/objects/global.c ext/src/api/objects/instance.c ext/src/api/objects/memory.c ext/src/api/objects/module.c ext/src/api/objects/table.c ext/src/api/objects/trap.c ext/src/api/objects/val.c)
set(WASMER_API_TYPES ext/src/api/types/exporttype.c ext/src/api/types/externtype.c ext/src/api/types/functype.c ext/src/api/types/globaltype.c ext/src/api/types/importtype.c ext/src/api/types/limits.c ext/src/api/types/memorytype.c ext/src/api/types/tabletype.c ext/src/api/types/valkind.c ext/src/api/types/valtype.c)
set(WASMER_SOURCES ext/src/php_wasmer.h ext/src/wasmer.c ext/src/wasmer_root_arginfo.h)
set(WASMER_ALL_SOURCES ${WASMER_API} ${WASMER_API_OBJECTS} ${WASMER_API_TYPES} ${WASMER_SOURCES})

execute_process (
        COMMAND php-config --include-dir
        OUTPUT_VARIABLE PHP_SOURCE
)
string(REGEX REPLACE "\n$" "" PHP_SOURCE "${PHP_SOURCE}")

message("Using source directory: ${PHP_SOURCE}")

include_directories(${PHP_SOURCE})
include_directories(${PHP_SOURCE}/main)
include_directories(${PHP_SOURCE}/Zend)
include_directories(${PHP_SOURCE}/TSRM)
include_directories(${PROJECT_SOURCE_DIR})
include_directories(ext/include)

add_custom_target(configure
        COMMAND phpize && ./configure
        DEPENDS ${WASMER_ALL_SOURCES}
        WORKING_DIRECTORY ${PROJECT_SOURCE_DIR})

add_library(___ EXCLUDE_FROM_ALL ${WASMER_ALL_SOURCES})
