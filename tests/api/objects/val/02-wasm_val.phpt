--TEST--
Val API

--FILE--
<?php

var_dump(wasm_val_i32(1));
var_dump(wasm_val_i64(2));
var_dump(wasm_val_f32(3.0));
var_dump(wasm_val_f64(4.0));

?>
--EXPECTF--
resource(%d) of type (wasm_val_t)
resource(%d) of type (wasm_val_t)
resource(%d) of type (wasm_val_t)
resource(%d) of type (wasm_val_t)
