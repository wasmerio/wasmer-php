--TEST--
Val API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_val_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_val_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(1)
string(4) "bool"
int(1)
NULL
