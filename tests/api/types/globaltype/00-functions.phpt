--TEST--
GlobalType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_globaltype_new'),
    function_exists('wasm_globaltype_delete'),
    function_exists('wasm_globaltype_content'),
    function_exists('wasm_globaltype_mutability'),
    function_exists('wasm_globaltype_copy'),
    function_exists('wasm_globaltype_as_externtype'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
