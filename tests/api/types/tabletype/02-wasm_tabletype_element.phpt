--TEST--
TableType API: wasm_tabletype_element

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$limits = wasm_limits_new(1, 2);
$tabletype = wasm_tabletype_new($valtype, $limits);
$kind = wasm_valtype_kind(wasm_tabletype_element($tabletype));
var_dump($kind == WASM_I32);

?>
--EXPECTF--
bool(true)
