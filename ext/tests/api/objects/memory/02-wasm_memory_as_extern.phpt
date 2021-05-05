--TEST--
Memory API: wasm_memory_as_extern

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);
var_dump(wasm_memory_as_extern($memory));

?>
--EXPECTF--
resource(%d) of type (wasm_extern_t)
