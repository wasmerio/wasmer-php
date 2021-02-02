--TEST--
Val API: Wasm\Vec\Val::offsetGet()

--FILE--
<?php

$val1 = wasm_val_i32(1);
$val2 = wasm_val_i32(2);
$vals = [$val1, $val2];
$vec = new Wasm\Vec\Val($vals);
var_dump($vec[0]);
var_dump($vec[1]);
try {
    var_dump($vec[2]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_val_t)
resource(%d) of type (wasm_val_t)
string(52) "Wasm\Vec\Val::offsetGet($offset) index out of bounds"
