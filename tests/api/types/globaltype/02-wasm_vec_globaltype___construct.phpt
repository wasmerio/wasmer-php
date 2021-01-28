--TEST--
GlobalType API: Wasm\Vec\GlobalType::construct()

--FILE--
<?php

$vec = new Wasm\Vec\GlobalType();
var_dump($vec);
var_dump(count($vec));

$vec = new Wasm\Vec\GlobalType(2);
var_dump($vec);
var_dump(count($vec));

$globaltype1 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$globaltype2 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$globaltype3 = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$valtypes = [$globaltype1, $globaltype2, $globaltype3];
$vec = new Wasm\Vec\GlobalType($valtypes);
var_dump($vec);
var_dump(count($vec));
--EXPECTF--
object(Wasm\Vec\GlobalType)#%d (%d) {
}
int(0)
object(Wasm\Vec\GlobalType)#%d (%d) {
}
int(2)
object(Wasm\Vec\GlobalType)#%d (%d) {
}
int(3)
