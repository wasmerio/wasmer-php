--TEST--
Module API: wasm_module_copy

--SKIPIF--
<?php
if (true) print 'skip wasm_module_copy not available';

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
$moduleCopy = wasm_module_copy($module);
var_dump($moduleCopy);

?>
--EXPECTF--
resource(%d) of type (wasm_module_t)
