--TEST--
Extern API: Wasm\Vec\Extern::construct()

--FILE--
<?php

$vec = new Wasm\Vec\Extern();
var_dump($vec);
var_dump(count($vec));

$vec = new Wasm\Vec\Extern(2);
var_dump($vec);
var_dump(count($vec));

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
$externs = [$globalExtern1, $globalExtern2, $globalExtern3];
$vec = new Wasm\Vec\Extern($externs);
var_dump($vec);
var_dump(count($vec));

?>
--EXPECTF--
object(Wasm\Vec\Extern)#%d (%d) {
}
int(0)
object(Wasm\Vec\Extern)#%d (%d) {
}
int(2)
object(Wasm\Vec\Extern)#%d (%d) {
}
int(3)
