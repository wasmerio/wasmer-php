--TEST--
Memory API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_memory_new'),
    function_exists('wasm_memory_type'),
    function_exists('wasm_memory_data'),
    function_exists('wasm_memory_data_size'),
    function_exists('wasm_memory_size'),
    function_exists('wasm_memory_grow'),
    function_exists('wasm_memory_copy'),
    function_exists('wasm_memory_same'),
    function_exists('wasm_memory_as_extern'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
