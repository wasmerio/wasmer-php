--TEST--
Func API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_func_new'),
    function_exists('wasm_func_as_extern'),
);

?>
--EXPECTF--
bool(true)
bool(true)
