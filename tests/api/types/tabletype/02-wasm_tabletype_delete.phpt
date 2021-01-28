--TEST--
TableType API: wasm_tabletype_delete

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$limits = wasm_limits_new(1, 2);
$tabletype = wasm_tabletype_new($valtype, $limits);
var_dump(wasm_tabletype_delete($tabletype));

try {
    wasm_tabletype_delete($tabletype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
bool(true)
string(83) "wasm_tabletype_delete(): supplied resource is not a valid wasm_tabletype_t resource"
