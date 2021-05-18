--TEST--
Memory API: wasm_memory_grow

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);

var_dump(wasm_memory_grow($memory, 1));

$size = wasm_memory_size($memory);
var_dump($size);

?>
--EXPECTF--
bool(true)
int(2)
