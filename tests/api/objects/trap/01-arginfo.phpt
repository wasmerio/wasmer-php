--TEST--
Trap API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_trap_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_trap_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_trap_message');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_trap_origin');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_trap_trace');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

?>
--EXPECTF--
int(2)
NULL
int(1)
NULL
int(1)
string(6) "string"
int(1)
NULL
int(1)
string(14) "Wasm\Vec\Frame"
