--TEST--
ImportType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_importtype_new'),
    function_exists('wasm_importtype_delete'),
    function_exists('wasm_importtype_module'),
    function_exists('wasm_importtype_name'),
    function_exists('wasm_importtype_type'),
    function_exists('wasm_importtype_copy'),
);

--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
