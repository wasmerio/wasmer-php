--TEST--
Limits API: wasm_limits_new

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
var_dump($limits);

?>
--EXPECTF--
resource(%d) of type (wasm_limits_t)
