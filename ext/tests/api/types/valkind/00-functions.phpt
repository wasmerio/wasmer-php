--TEST--
ValKind API functions

--FILE--
<?php

var_dump(
    function_exists('wasm_valkind_is_num'),
    function_exists('wasm_valkind_is_ref'),
);

?>
--EXPECTF--
bool(true)
bool(true)
