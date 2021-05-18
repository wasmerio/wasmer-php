--TEST--
Memory API: wasm_memory_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
var_dump(wasm_memory_new($store, $memorytype))
?>
--EXPECTF--
resource(%d) of type (wasm_memory_t)
