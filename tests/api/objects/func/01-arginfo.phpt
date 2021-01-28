--TEST--
Func API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_func_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_func_as_extern');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

--EXPECTF--
int(3)
NULL
int(1)
NULL
