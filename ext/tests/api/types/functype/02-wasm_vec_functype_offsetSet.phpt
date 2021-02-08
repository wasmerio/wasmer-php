--TEST--
FuncType API: Wasm\Vec\FuncType::offsetSet()

--FILE--
<?php

$functype1 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functype2 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functype3 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$vec = new Wasm\Vec\FuncType(2);
$vec[0] = $functype1;
var_dump($vec[0]);
$vec[1] = $functype2;
var_dump($vec[1]);

try {
    $vec[2] = $functype3;
} catch (Wasm\Exception\OutOfBoundsException $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_functype_t)
resource(%d) of type (wasm_functype_t)
string(57) "Wasm\Vec\FuncType::offsetSet($offset) index out of bounds"
