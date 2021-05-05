--TEST--
Memory API: wasm_memory_data_size

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);
$size = wasm_memory_data_size($memory);
var_dump($size);
?>
--EXPECTF--
int(65536)
