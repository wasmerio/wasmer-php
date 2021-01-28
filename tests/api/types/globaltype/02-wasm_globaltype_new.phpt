--TEST--
GlobalType API: wasm_globaltype_new

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
var_dump($globaltype);

--EXPECTF--
resource(%d) of type (wasm_globaltype_t)
