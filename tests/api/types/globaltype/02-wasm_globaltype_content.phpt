--TEST--
GlobalType API: wasm_globaltype_content

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$kind = wasm_valtype_kind(wasm_globaltype_content($globaltype));
var_dump($kind == WASM_I32);

--EXPECTF--
bool(true)
