--TEST--
TableType API: Wasm\Vec\TableType::construct()

--FILE--
<?php

$vec = new Wasm\Vec\TableType();
var_dump($vec);
var_dump(count($vec));

$vec = new Wasm\Vec\TableType(2);
var_dump($vec);
var_dump(count($vec));

$tabletype1 = wasm_tabletype_new(wasm_valtype_new(WASM_I32), wasm_limits_new(1, 2));
$tabletype2 = wasm_tabletype_new(wasm_valtype_new(WASM_I32), wasm_limits_new(1, 2));
$tabletype3 = wasm_tabletype_new(wasm_valtype_new(WASM_I32), wasm_limits_new(1, 2));
$tabletypes = [$tabletype1, $tabletype2, $tabletype3];
$vec = new Wasm\Vec\TableType($tabletypes);
var_dump($vec);
var_dump(count($vec));

?>
--EXPECTF--
object(Wasm\Vec\TableType)#%d (%d) {
}
int(0)
object(Wasm\Vec\TableType)#%d (%d) {
}
int(2)
object(Wasm\Vec\TableType)#%d (%d) {
}
int(3)
