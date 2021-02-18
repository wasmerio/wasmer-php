--TEST--
Limits API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_limits_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(2)
NULL
