--TEST--
ValType API: wasm_valtype_is_num

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$isNum = wasm_valtype_is_num($valtype);
var_dump($isNum);
wasm_valtype_delete($valtype);

$valtype = wasm_valtype_new(WASM_ANYREF);
$isNum = wasm_valtype_is_num($valtype);
var_dump($isNum);
wasm_valtype_delete($valtype);

?>
--EXPECTF--
bool(true)
bool(false)
