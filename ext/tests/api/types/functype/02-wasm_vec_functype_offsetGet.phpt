--TEST--
FuncType API: Wasm\Vec\FuncType::offsetGet()

--FILE--
<?php

$functype1 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functype2 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functypes = [$functype1, $functype2];
$vec = new Wasm\Vec\FuncType($functypes);
var_dump($vec[0]);
var_dump($vec[1]);
try {
    var_dump($vec[2]);
} catch (Wasm\Exception\OutOfBoundsException $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_functype_t)
resource(%d) of type (wasm_functype_t)
string(57) "Wasm\Vec\FuncType::offsetGet($offset) index out of bounds"
