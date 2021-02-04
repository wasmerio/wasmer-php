--TEST--
Trap API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_trap_new'),
    function_exists('wasm_trap_copy'),
    function_exists('wasm_trap_message'),
    function_exists('wasm_trap_origin'),
    function_exists('wasm_trap_trace'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
