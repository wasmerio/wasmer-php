--TEST--
ExternType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_externtype_delete'),
    function_exists('wasm_externtype_kind'),
    function_exists('wasm_externtype_as_functype'),
    function_exists('wasm_externtype_as_globaltype'),
    function_exists('wasm_externtype_as_memorytype'),
    function_exists('wasm_externtype_as_tabletype'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
