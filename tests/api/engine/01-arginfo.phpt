--TEST--
Engine API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_engine_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_engine_new_with_config');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_engine_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

?>
--EXPECTF--
int(0)
NULL
int(1)
NULL
int(1)
string(4) "bool"
