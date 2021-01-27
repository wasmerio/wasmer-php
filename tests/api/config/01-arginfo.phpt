--TEST--
Config API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_config_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(0)
NULL
