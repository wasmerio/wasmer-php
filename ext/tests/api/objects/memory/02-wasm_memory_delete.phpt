--TEST--
Memory API: wasm_memory_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memory = wasm_memory_new($store, $memorytype);
var_dump(wasm_memory_delete($memory));

try {
    wasm_memory_delete($memory);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(77) "wasm_memory_delete(): supplied resource is not a valid wasm_memory_t resource"
