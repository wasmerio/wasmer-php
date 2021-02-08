--TEST--
Limits API: wasm_limits_min

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
var_dump(wasm_limits_min($limits));

?>
--EXPECTF--
int(1)
