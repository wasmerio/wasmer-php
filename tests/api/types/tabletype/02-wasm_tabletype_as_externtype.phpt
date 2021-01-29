--TEST--
TableType API: wasm_tabletype_as_externtype

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$limits = wasm_limits_new(1, 2);
$tabletype = wasm_tabletype_new($valtype, $limits);
$externtype = wasm_tabletype_as_externtype($tabletype);
var_dump($externtype);

?>
--EXPECTF--
resource(%d) of type (wasm_externtype_t)
