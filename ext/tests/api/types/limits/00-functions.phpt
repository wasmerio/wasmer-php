--TEST--
Limits API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_limits_new'),
);

?>
--EXPECTF--
bool(true)
