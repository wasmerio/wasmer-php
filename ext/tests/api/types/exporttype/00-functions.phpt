--TEST--
ExportType API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_exporttype_new'),
    function_exists('wasm_exporttype_delete'),
    function_exists('wasm_exporttype_name'),
    function_exists('wasm_exporttype_type'),
    function_exists('wasm_exporttype_copy'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
