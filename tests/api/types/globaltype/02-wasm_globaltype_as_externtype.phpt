--TEST--
GlobalType API: wasm_globaltype_as_externtype

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$externtype = wasm_globaltype_as_externtype($globaltype);
var_dump($externtype);

--EXPECTF--
resource(%d) of type (wasm_externtype_t)
