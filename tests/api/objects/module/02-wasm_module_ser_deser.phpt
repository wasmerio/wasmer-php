--TEST--
Module API: wasm_module_serialize and wasm_module_deserialize

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
var_dump($module);

$ser = wasm_module_serialize($module);
$deser = wasm_module_deserialize($store, $ser);
var_dump($deser);

?>
--EXPECTF--
resource(%d) of type (wasm_module_t)
resource(%d) of type (wasm_module_t)
