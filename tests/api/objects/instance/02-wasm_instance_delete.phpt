--TEST--
Instance API: wasm_instance_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
var_dump(wasm_instance_delete($instance));

try {
    wasm_instance_delete($instance);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(81) "wasm_instance_delete(): supplied resource is not a valid wasm_instance_t resource"
