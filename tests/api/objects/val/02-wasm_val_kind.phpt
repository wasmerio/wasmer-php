--TEST--
Val API: wasm_val_kind

--FILE--
<?php

var_dump(wasm_val_kind(wasm_val_i32(1)) == WASM_I32);
var_dump(wasm_val_kind(wasm_val_i64(2)) == WASM_I64);
var_dump(wasm_val_kind(wasm_val_f32(3.0)) == WASM_F32);
var_dump(wasm_val_kind(wasm_val_f32(3)) == WASM_F32);
var_dump(wasm_val_kind(wasm_val_f64(4.0)) == WASM_F64);
var_dump(wasm_val_kind(wasm_val_f64(4)) == WASM_F64);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
