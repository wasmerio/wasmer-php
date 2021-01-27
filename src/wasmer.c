#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"
#include "Zend/zend_interfaces.h"

#include "php_wasmer.h"
#include "wasmer_arginfo.h"

///////////////////////////////////////////////////////////////////////////////
// Runtime Environment

///////////////////////////////////////////////////////////////////////////////
// Type Representations

///////////////////////////////////////////////////////////////////////////////
// Runtime Objects

///////////////////////////////////////////////////////////////////////////////

PHP_MINIT_FUNCTION(wasmer) { return SUCCESS; }

PHP_RINIT_FUNCTION(wasmer) {
#if defined(ZTS) && defined(COMPILE_DL_WASMER)
  ZEND_TSRMLS_CACHE_UPDATE();
#endif

  return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(wasmer) { return SUCCESS; }

PHP_MSHUTDOWN_FUNCTION(wasmer) { return SUCCESS; }

PHP_MINFO_FUNCTION(wasmer) {
  php_info_print_table_start();
  php_info_print_table_end();
}

zend_module_entry wasmer_module_entry = {
    STANDARD_MODULE_HEADER, "wasmer",
    ext_functions,          PHP_MINIT(wasmer),
    PHP_MSHUTDOWN(wasmer),  PHP_RINIT(wasmer),
    PHP_RSHUTDOWN(wasmer),  PHP_MINFO(wasmer),
    PHP_WASMER_VERSION,     STANDARD_MODULE_PROPERTIES};

#ifdef COMPILE_DL_WASMER
#ifdef ZTS
ZEND_TSRMLS_CACHE_DEFINE()
#endif
ZEND_GET_MODULE(wasmer)
#endif