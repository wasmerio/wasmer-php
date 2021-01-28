--TEST--
TableType API: Wasm\Vec\TableType::offsetSet()

--FILE--
<?php
$tabletype1 = wasm_tabletype_new(wasm_valtype_new(WASM_I32), wasm_limits_new(1, 2));
$tabletype2 = wasm_tabletype_new(wasm_valtype_new(WASM_I32), wasm_limits_new(1, 2));
$vec = new Wasm\Vec\TableType(2);
$vec[0] = $tabletype1;
var_dump($vec[0]);
$vec[1] = $tabletype2;
var_dump($vec[1]);

try {
    $vec[2] = $tabletype2;
} catch (Exception $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
resource(%d) of type (wasm_tabletype_t)
resource(%d) of type (wasm_tabletype_t)
string(58) "Wasm\Vec\TableType::offsetSet($offset) index out of bounds"
