--TEST--
TableType API: Wasm\Vec\TableType::offsetGet()

--FILE--
<?php
$valtype1 = wasm_valtype_new(WASM_I32);
$limits1 = wasm_limits_new(1, 2);
$tabletype1 = wasm_tabletype_new($valtype1, $limits1);
$valtype2 = wasm_valtype_new(WASM_I32);
$limits2 = wasm_limits_new(1, 2);
$tabletype2 = wasm_tabletype_new($valtype1, $limits1);
$tabletypes = [$tabletype1, $tabletype2];
$vec = new Wasm\Vec\TableType($tabletypes);
var_dump($vec[0]);
var_dump($vec[1]);
try {
    var_dump($vec[2]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
resource(%d) of type (wasm_tabletype_t)
resource(%d) of type (wasm_tabletype_t)
string(58) "Wasm\Vec\TableType::offsetGet($offset) index out of bounds"
