# Changelog

*The format is based on [Keep a Changelog].*

[Keep a Changelog]: http://keepachangelog.com/en/1.0.0/


## **[1.0.0] - 2021-02-22**

### Added

* Object-oriented interface through `wasm/wasm` library

## **[1.0.0-beta1] - 2021-02-02**

### Breaking changes

This release is basically **a complete rewrite of Wasmer PHP extension**. Previous releases were built on top of a 
non-standard API. We are now using the standard [Wasm C API][wasm-c-api]:

```php
<?php declare(strict_types=1);

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$wasm = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.wasm');

$module = wasm_module_new($store, $wasm);

function hello_callback() {
    echo 'Hello Wasmer (PHP)!' . PHP_EOL;
}

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = wasm_func_new($store, $functype, 'hello_callback');
wasm_functype_delete($functype);

$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
$instance = wasm_instance_new($store, $module, $externs);

wasm_func_delete($func);

$exports = wasm_instance_exports($instance);
$run = wasm_extern_as_func($exports[0]);

wasm_module_delete($module);
wasm_instance_delete($instance);

wasm_store_delete($store);
wasm_engine_delete($engine);

wasm_func_call($run, new Wasm\Vec\Val());
```

⚠️ Expect everything from previous releases to be non-backward compatible.


[wasm-c-api]: https://github.com/WebAssembly/wasm-c-api

### Added

* Implement the `config` API
* Implement the `engine` API
* Implement the `store` API
* Implement the `wasmer` non-standard API
* Implement the `wat` non-standard API
* Implement the `exporttype` API
* Implement the `externtype` API
* Implement the `functype` API
* Implement the `globaltype` API
* Implement the `importtype` API
* Implement the `memorytype` API
* Implement the `tabletype` API
* Implement the `valtype` API
* Implement the `valtype` API
* Implement the `extern` API
* Implement the `func` API
* Implement the `global` API
* Implement the `instance` API
* Implement the `module` API
* Implement the `trap` API


[1.0.0]: https://github.com/wasmerio/wasmer-php/tree/1.0.0/README.md
[1.0.0-beta1]: https://github.com/wasmerio/wasmer-php/tree/1.0.0-beta1/README.md
