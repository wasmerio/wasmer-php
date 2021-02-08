--TEST--
Extern API: wasm_extern_type

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
$extern = wasm_global_as_extern($global);

var_dump(wasm_extern_type($extern));

?>
--EXPECTF--
resource(%d) of type (wasm_externtype_t)
