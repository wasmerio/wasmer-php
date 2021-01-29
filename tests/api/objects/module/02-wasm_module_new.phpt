--TEST--
Module API: wasm_module_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
var_dump($module);

?>
--EXPECTF--
resource(%d) of type (wasm_module_t)
