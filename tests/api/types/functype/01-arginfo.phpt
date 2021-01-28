--TEST--
FuncType API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_functype_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_functype_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_functype_params');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_functype_results');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_functype_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_functype_as_externtype');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

--EXPECTF--
int(2)
NULL
int(1)
string(4) "bool"
int(1)
string(16) "Wasm\Vec\ValType"
int(1)
string(16) "Wasm\Vec\ValType"
int(1)
NULL
int(1)
NULL
