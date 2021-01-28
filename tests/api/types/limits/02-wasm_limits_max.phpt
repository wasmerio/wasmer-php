--TEST--
Limits API: wasm_limits_max

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
var_dump(wasm_limits_max($limits));

--EXPECTF--
int(2)
