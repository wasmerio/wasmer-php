--TEST--
ValType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_valtype_new'),
    function_exists('wasm_valtype_delete'),
    function_exists('wasm_valtype_kind'),
    function_exists('wasm_valtype_is_num'),
    function_exists('wasm_valtype_is_ref'),
    function_exists('wasm_valtype_copy'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
