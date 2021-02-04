--TEST--
Module API: wasm_module_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
var_dump(wasm_module_delete($module));

try {
    wasm_module_delete($module);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(77) "wasm_module_delete(): supplied resource is not a valid wasm_module_t resource"
