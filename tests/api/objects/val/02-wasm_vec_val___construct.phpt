--TEST--
Val API: Wasm\Vec\Val::construct()

--FILE--
<?php

$vec = new Wasm\Vec\Val();
var_dump($vec);
var_dump(count($vec));

$vec = new Wasm\Vec\Val(2);
var_dump($vec);
var_dump(count($vec));

$functype1 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functype2 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functype3 = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functypes = [$functype1, $functype2, $functype3];
$vec = new Wasm\Vec\Val($functypes);
var_dump($vec);
var_dump(count($vec));

?>
--EXPECTF--
object(Wasm\Vec\Val)#%d (%d) {
}
int(0)
object(Wasm\Vec\Val)#%d (%d) {
}
int(2)
object(Wasm\Vec\Val)#%d (%d) {
}
int(3)
