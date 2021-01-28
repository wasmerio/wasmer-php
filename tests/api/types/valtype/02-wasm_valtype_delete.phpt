--TEST--
ValType API: wasm_valtype_delete

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
var_dump(wasm_valtype_delete($valtype));

try {
    wasm_valtype_delete($valtype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
bool(true)
string(79) "wasm_valtype_delete(): supplied resource is not a valid wasm_valtype_t resource"
