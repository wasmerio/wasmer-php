--TEST--
GlobalType API: wasm_globaltype_mutability

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$mutability = wasm_globaltype_mutability($globaltype);
var_dump($mutability == WASM_CONST);

?>
--EXPECTF--
bool(true)
