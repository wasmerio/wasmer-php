--TEST--
FuncType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_functype_new'),
    function_exists('wasm_functype_params'),
    function_exists('wasm_functype_results'),
    function_exists('wasm_functype_delete'),
    function_exists('wasm_functype_copy'),
    function_exists('wasm_functype_as_externtype'),
);

--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
