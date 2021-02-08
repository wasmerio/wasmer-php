--TEST--
Store API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_store_new'),
    function_exists('wasm_store_delete'),
);

?>
--EXPECTF--
bool(true)
bool(true)
