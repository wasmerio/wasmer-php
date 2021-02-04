--TEST--
Val API

--FILE--
<?php declare(strict_types=1);

$i32 = wasm_val_i32(1);
var_dump(wasm_val_value($i32));

$f32 = wasm_val_f32(2.0);
var_dump(wasm_val_value($f32));

$f32int = wasm_val_f32(2);
var_dump(wasm_val_value($f32int));

$i64 = wasm_val_i64(3);
var_dump(wasm_val_value($i64));

$f64 = wasm_val_f64(4.0);
var_dump(wasm_val_value($f64));

$f64int = wasm_val_f64(4);
var_dump(wasm_val_value($f64int));

var_dump(wasm_val_value(wasm_val_i32(5)));

?>
--EXPECTF--
int(1)
float(2)
float(2)
int(3)
float(4)
float(4)
int(5)
