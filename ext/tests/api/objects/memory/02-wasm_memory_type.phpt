--TEST--
Memory API: wasm_memory_type

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);
$type = wasm_memory_type($memory);
var_dump($type);
$limits = wasm_memorytype_limits($type);
var_dump($limits);
?>
--EXPECTF--
resource(%d) of type (wasm_memorytype_t)
resource(%d) of type (wasm_limits_t)
