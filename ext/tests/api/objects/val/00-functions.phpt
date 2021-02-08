--TEST--
Val API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_val_delete'),
    function_exists('wasm_val_copy'),
);

?>
--EXPECTF--
bool(true)
bool(true)
