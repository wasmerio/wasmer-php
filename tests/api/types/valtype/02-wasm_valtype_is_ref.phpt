--TEST--
ValType API: wasm_valtype_is_ref

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_ANYREF);
$isRef = wasm_valtype_is_ref($valtype);
var_dump($isRef);

$valtype = wasm_valtype_new(WASM_I32);
$isRef = wasm_valtype_is_ref($valtype);
var_dump($isRef);

--EXPECTF--
bool(true)
bool(false)
