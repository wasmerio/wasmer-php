--TEST--
Extern API: Wasm\Vec\Extern::offsetGet()

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype1 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global1 = wasm_global_new($store, $globaltype1, wasm_val_i32(1));
$globalExtern1 = wasm_global_as_extern($global1);
$globaltype2 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global2 = wasm_global_new($store, $globaltype2, wasm_val_i32(1));
$globalExtern2 = wasm_global_as_extern($global2);
$externs = [$globalExtern1, $globalExtern2];
$vec = new Wasm\Vec\Extern($externs);
var_dump($vec[0]);
var_dump($vec[1]);
try {
    var_dump($vec[2]);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_extern_t)
resource(%d) of type (wasm_extern_t)
string(55) "Wasm\Vec\Extern::offsetGet($offset) index out of bounds"
