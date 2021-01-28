--TEST--
MemoryType API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_memorytype_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_memorytype_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_memorytype_limits');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_memorytype_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

--EXPECTF--
int(1)
NULL
int(1)
string(4) "bool"
int(1)
NULL
int(1)
NULL
