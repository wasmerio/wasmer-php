--TEST--
GlobalType API: wasm_globaltype_delete

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
var_dump(wasm_globaltype_delete($globaltype));

try {
    wasm_globaltype_delete($globaltype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
bool(true)
string(85) "wasm_globaltype_delete(): supplied resource is not a valid wasm_globaltype_t resource"
