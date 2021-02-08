--TEST--
Extern API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_extern_kind'),
    function_exists('wasm_extern_type'),
    function_exists('wasm_extern_as_func'),
    function_exists('wasm_extern_as_global'),
    function_exists('wasm_extern_as_table'),
    function_exists('wasm_extern_as_memory'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
