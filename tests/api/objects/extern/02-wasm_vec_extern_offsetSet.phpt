--TEST--
Extern API: Wasm\Vec\Extern::offsetSet()

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
$globaltype3 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global3 = wasm_global_new($store, $globaltype3, wasm_val_i32(1));
$globalExtern3 = wasm_global_as_extern($global3);
$vec = new Wasm\Vec\Extern(2);
$vec[0] = $globalExtern1;
var_dump($vec[0]);
$vec[1] = $globalExtern2;
var_dump($vec[1]);

try {
    $vec[2] = $globalExtern3;
} catch (Wasm\Exception\OutOfBoundsException $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_extern_t)
resource(%d) of type (wasm_extern_t)
string(55) "Wasm\Vec\Extern::offsetSet($offset) index out of bounds"
