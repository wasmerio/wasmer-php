--TEST--
Module API: wasm_module_exports

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module (global $one (export "one") i32 (i32.const 1)))');
$module = wasm_module_new($store, $wasm);
$exports = wasm_module_exports($module);
var_dump($exports);
var_dump($exports->count());

?>
--EXPECTF--
object(Wasm\Vec\ExportType)#%d (0) {
}
int(1)
