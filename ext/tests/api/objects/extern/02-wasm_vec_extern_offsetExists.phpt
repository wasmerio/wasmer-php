--TEST--
Extern API: Wasm\Vec\Extern::offsetExists()

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
$globalExtern = wasm_global_as_extern($global);
$externs = [$globalExtern];
$vec = new Wasm\Vec\Extern($externs);

var_dump(isset($vec[0]));
var_dump($vec->offsetExists(0));
var_dump(isset($vec[1]));
var_dump($vec->offsetExists(1));

?>
--EXPECTF--
bool(true)
bool(true)
bool(false)
bool(false)
