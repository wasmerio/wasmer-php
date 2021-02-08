--TEST--
ExportType API functions arguments information

--FILE--
<?php

$reflection = new ReflectionFunction('wasm_exporttype_new');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_exporttype_delete');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_exporttype_name');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType()->getName());

$reflection = new ReflectionFunction('wasm_exporttype_type');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

$reflection = new ReflectionFunction('wasm_exporttype_copy');
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getReturnType());

?>
--EXPECTF--
int(2)
NULL
int(1)
string(4) "bool"
int(1)
string(6) "string"
int(1)
NULL
int(1)
NULL
