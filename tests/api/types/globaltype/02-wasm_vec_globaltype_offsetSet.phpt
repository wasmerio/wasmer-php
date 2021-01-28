--TEST--
GlobalType API: Wasm\Vec\GlobalType::offsetSet()

--FILE--
<?php
$valtype1 = wasm_valtype_new(WASM_I32);
$globaltype1 = wasm_globaltype_new($valtype1, WASM_CONST);
$valtype2 = wasm_valtype_new(WASM_I32);
$globaltype2 = wasm_globaltype_new($valtype2, WASM_CONST);
$valtypes = [$globaltype1];
$vec = new Wasm\Vec\GlobalType(2);
$vec[0] = $globaltype1;
var_dump($vec[0]);
$vec[1] = $globaltype2;
var_dump($vec[1]);

try {
    $vec[2] = $globaltype2;
} catch (Exception $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
resource(%d) of type (wasm_globaltype_t)
resource(%d) of type (wasm_globaltype_t)
string(59) "Wasm\Vec\GlobalType::offsetSet($offset) index out of bounds"
