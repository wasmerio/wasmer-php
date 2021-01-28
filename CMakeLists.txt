cmake_minimum_required(VERSION 3.17)
project(wasmer-php C)

add_compile_definitions(HAVE_WASMER)

set(WASMER_API src/api/config.c src/api/engine.c src/api/store.c src/api/wasmer.c)
set(WASMER_API_OBJECTS)
set(WASMER_API_TYPES src/api/types/limits.c src/api/types/valkind.c src/api/types/valtype.c)
set(WASMER_SOURCES src/php_wasmer.h src/wasmer.c src/wasmer_arginfo.h)
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
include_directories(${PROJECT_SOURCE_DIR}/include)

add_custom_target(configure
        COMMAND phpize && ./configure
        DEPENDS ${WASMER_ALL_SOURCES}
        WORKING_DIRECTORY ${PROJECT_SOURCE_DIR})

add_library(___ EXCLUDE_FROM_ALL ${WASMER_ALL_SOURCES})
