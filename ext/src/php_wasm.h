#ifndef PHP_WASM_H
#define PHP_WASM_H

extern zend_module_entry wasm_module_entry;
#define phpext_wasm_ptr &wasm_module_entry

#define PHP_WASM_VERSION "1.0.0-beta1"

#if defined(ZTS) && defined(COMPILE_DL_WASMER)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#endif /* PHP_WASM_H */