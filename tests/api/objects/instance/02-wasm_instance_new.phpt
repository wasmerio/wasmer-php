--TEST--
Instance API: wasm_instance_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
var_dump($instance);

?>

--EXPECTF--
resource(%d) of type (wasm_instance_t)
