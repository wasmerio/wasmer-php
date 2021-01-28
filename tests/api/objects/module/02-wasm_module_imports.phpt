--TEST--
Module API: wasm_module_imports

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module (global $some (import "env" "some") i32))');
$module = wasm_module_new($store, $wasm);
$imports = wasm_module_imports($module);
var_dump($imports);
var_dump($imports->count());

--EXPECTF--
object(Wasm\Vec\ImportType)#%d (0) {
}
int(1)
