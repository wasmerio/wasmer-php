--TEST--
ValType API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_valtype_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_valtype_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_valtype_kind');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_valtype_is_num');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_valtype_is_ref');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_valtype_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

--EXPECTF--
int(1)
NULL
int(1)
string(4) "bool"
int(1)
string(3) "int"
int(1)
string(4) "bool"
int(1)
string(4) "bool"
int(1)
NULL
