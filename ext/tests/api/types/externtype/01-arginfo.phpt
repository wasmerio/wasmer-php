--TEST--
ExternType API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_externtype_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_externtype_kind');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_externtype_as_functype');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_externtype_as_globaltype');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_externtype_as_memorytype');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_externtype_as_tabletype');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(1)
string(4) "bool"
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
