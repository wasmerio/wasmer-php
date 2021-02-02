--TEST--
ValType API: Wasm\Vec\ValType::construct()

--FILE--
<?php

$vec = new Wasm\Vec\ValType();
var_dump($vec);
var_dump(count($vec));

$vec = new Wasm\Vec\ValType(2);
var_dump($vec);
var_dump(count($vec));

$valtype1 = wasm_valtype_new(WASM_I32);
$valtype2 = wasm_valtype_new(WASM_I64);
$valtypes = [$valtype1, $valtype2, wasm_valtype_new(WASM_F32)];
$vec = new Wasm\Vec\ValType($valtypes);
var_dump($vec);
var_dump(count($vec));

?>
--EXPECTF--
object(Wasm\Vec\ValType)#%d (%d) {
}
int(0)
object(Wasm\Vec\ValType)#%d (%d) {
}
int(2)
object(Wasm\Vec\ValType)#%d (%d) {
}
int(3)
