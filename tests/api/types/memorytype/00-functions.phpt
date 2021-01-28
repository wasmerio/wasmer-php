--TEST--
MemoryType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_memorytype_new'),
    function_exists('wasm_memorytype_delete'),
    function_exists('wasm_memorytype_limits'),
    function_exists('wasm_memorytype_copy'),
);

--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
