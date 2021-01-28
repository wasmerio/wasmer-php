--TEST--
ValType API: Wasm\Vec\ValType::offsetGet()

--FILE--
<?php
$valtype1 = wasm_valtype_new(WASM_I32);
$valtype2 = wasm_valtype_new(WASM_I32);
$valtypes = [$valtype1, $valtype2];
$vec = new Wasm\Vec\ValType($valtypes);
var_dump($vec[0]);
var_dump($vec[1]);
try {
    var_dump($vec[2]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
resource(%d) of type (wasm_valtype_t)
resource(%d) of type (wasm_valtype_t)
string(56) "Wasm\Vec\ValType::offsetGet($offset) index out of bounds"
