--TEST--
Module API: wasm_module_validate

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
var_dump(wasm_module_validate($store, $wasm));

try {
    wasm_module_validate($store, 'invalid');
} catch (Wasm\Exception\RuntimeException $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(48) "Validation error: Bad magic number (at offset 0)"
