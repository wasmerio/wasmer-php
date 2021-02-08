#ifndef PHP_WASMER_H
#define PHP_WASMER_H

extern zend_module_entry wasmer_module_entry;
#define phpext_wasmer_ptr &wasmer_module_entry

#define PHP_WASMER_VERSION "1.0.0-beta1"

#if defined(ZTS) && defined(COMPILE_DL_WASMER)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#endif /* PHP_WASMER_H */