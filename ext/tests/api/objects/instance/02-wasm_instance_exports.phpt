--TEST--
Instance API: wasm_instance_exports

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module (global $one (export "one") i32 (i32.const 1)))');
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
$exports = wasm_instance_exports($instance);
var_dump($exports);
var_dump($exports->count());

?>
--EXPECTF--
object(Wasm\Vec\Extern)#%d (0) {
}
int(1)
