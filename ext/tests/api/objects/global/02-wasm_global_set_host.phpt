--TEST--
Global API: wasm_global_set (host)

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$vartype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_VAR);
$consttype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$var = wasm_global_new($store, $vartype, wasm_val_i32(1));
$const = wasm_global_new($store, $consttype, wasm_val_i32(2));
wasm_global_set($var, wasm_val_i32(42));

var_dump(wasm_val_value(wasm_global_get($var)));

try {
    wasm_global_set($const, wasm_val_i32(42));
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
int(42)
string(50) "RuntimeError: Attempted to set an immutable global"
