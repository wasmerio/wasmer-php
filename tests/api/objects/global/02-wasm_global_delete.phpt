--TEST--
Global API: wasm_global_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
var_dump(wasm_global_delete($global));

try {
    wasm_global_delete($global);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(77) "wasm_global_delete(): supplied resource is not a valid wasm_global_t resource"
