--TEST--
Val API: wasm_val_delete

--FILE--
<?php

$val = wasm_val_i32(1);
var_dump(wasm_val_delete($val));

try {
    wasm_val_delete($val);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(71) "wasm_val_delete(): supplied resource is not a valid wasm_val_t resource"
