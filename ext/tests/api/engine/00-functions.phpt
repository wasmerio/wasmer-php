--TEST--
Engine API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_engine_new'),
    function_exists('wasm_engine_new_with_config'),
    function_exists('wasm_engine_delete'),
);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
