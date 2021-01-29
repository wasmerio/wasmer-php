--TEST--
Val API: Wasm\Vec\Val::offsetSet()

--FILE--
<?php
$val1 = wasm_val_i32(1);
$val2 = wasm_val_i32(2);
$vec = new Wasm\Vec\Val(2);
$vec[0] = $val1;
var_dump($vec[0]);
$vec[1] = $val2;
var_dump($vec[1]);

try {
    $vec[2] = $val2;
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_val_t)
resource(%d) of type (wasm_val_t)
string(52) "Wasm\Vec\Val::offsetSet($offset) index out of bounds"
