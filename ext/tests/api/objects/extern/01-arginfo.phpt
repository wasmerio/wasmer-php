--TEST--
Extern API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_extern_kind');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_extern_type');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_extern_as_func');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_extern_as_global');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_extern_as_table');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_extern_as_memory');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(1)
string(3) "int"
int(1)
NULL
int(1)
NULL
int(1)
NULL
int(1)
NULL
int(1)
NULL
