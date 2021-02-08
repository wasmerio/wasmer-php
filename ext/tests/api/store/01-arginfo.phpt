--TEST--
Store API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_store_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_store_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

?>
--EXPECTF--
int(1)
NULL
int(1)
string(4) "bool"
