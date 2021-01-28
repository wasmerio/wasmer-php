--TEST--
Module API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_module_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_module_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_validate');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_imports');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_exports');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_serialize');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_deserialize');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_module_name');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_module_set_name');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

--EXPECTF--
int(2)
NULL
int(1)
string(4) "bool"
int(2)
string(4) "bool"
int(1)
string(19) "Wasm\Vec\ImportType"
int(1)
string(19) "Wasm\Vec\ExportType"
int(1)
string(6) "string"
int(2)
NULL
int(1)
string(6) "string"
int(2)
string(4) "bool"
