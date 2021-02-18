#include "php.h"

#include "wasmer_wasm.h"

PHP_FUNCTION(wasmer_version) {
  ZEND_PARSE_PARAMETERS_NONE();

  RETURN_STRING(wasmer_version());
}

PHP_FUNCTION(wasmer_version_major) {
  ZEND_PARSE_PARAMETERS_NONE();

  RETURN_LONG(wasmer_version_major());
}

PHP_FUNCTION(wasmer_version_minor) {
  ZEND_PARSE_PARAMETERS_NONE();

  RETURN_LONG(wasmer_version_minor());
}

PHP_FUNCTION(wasmer_version_patch) {
  ZEND_PARSE_PARAMETERS_NONE();

  RETURN_LONG(wasmer_version_patch());
}

PHP_FUNCTION(wasmer_version_pre) {
  ZEND_PARSE_PARAMETERS_NONE();

  RETURN_STRING(wasmer_version_pre());
}
