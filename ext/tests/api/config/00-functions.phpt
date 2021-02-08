--TEST--
Config API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_config_new'),
    function_exists('wasm_config_delete'),
);

?>
--EXPECTF--
bool(true)
bool(true)
