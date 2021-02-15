--TEST--
Global API: wasm_global_type

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I64), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
var_dump(wasm_global_type($global));
var_dump(wasm_globaltype_content(wasm_global_type($global)));
var_dump(wasm_valtype_kind(wasm_globaltype_content(wasm_global_type($global))));

?>
--EXPECTF--
resource(%d) of type (wasm_globaltype_t)
resource(%d) of type (wasm_valtype_t)
int(0)
