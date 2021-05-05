--TEST--
Memory API: wasm_memory_same

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);
$copy = wasm_memory_copy($memory);

var_dump(wasm_memory_same($memory, $copy));

?>
--EXPECTF--
bool(true)
