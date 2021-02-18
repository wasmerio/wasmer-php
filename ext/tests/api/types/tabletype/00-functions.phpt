--TEST--
TableType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_tabletype_new'),
    function_exists('wasm_tabletype_delete'),
    function_exists('wasm_tabletype_element'),
    function_exists('wasm_tabletype_limits'),
    function_exists('wasm_tabletype_copy'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
