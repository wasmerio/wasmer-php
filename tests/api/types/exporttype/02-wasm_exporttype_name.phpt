--TEST--
ExportType API: wasm_exporttype_module

--FILE--
<?php

$name = "name";
$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$externtype = wasm_globaltype_as_externtype($globaltype);
$exporttype = wasm_exporttype_new($name, $externtype);
var_dump(wasm_exporttype_name($exporttype) === $name);

?>
--EXPECTF--
bool(true)
