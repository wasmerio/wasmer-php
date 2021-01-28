--TEST--
ValType API: Wasm\Vec\ValType::offsetSet()

--FILE--
<?php
$valtype1 = wasm_valtype_new(WASM_I32);
$valtype2 = wasm_valtype_new(WASM_I64);
$vec = new Wasm\Vec\ValType(2);
$vec[0] = $valtype1;
var_dump($vec[0]);
$vec[1] = $valtype2;
var_dump($vec[1]);

try {
    $vec[2] = $valtype2;
} catch (Exception $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
resource(%d) of type (wasm_valtype_t)
resource(%d) of type (wasm_valtype_t)
string(56) "Wasm\Vec\ValType::offsetSet($offset) index out of bounds"
