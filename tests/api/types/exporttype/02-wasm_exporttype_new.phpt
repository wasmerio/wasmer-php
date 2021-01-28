--TEST--
ExportType API: wasm_exporttype_new

--FILE--
<?php

$name = "name";
$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$externtype = wasm_globaltype_as_externtype($globaltype);
$exporttype = wasm_exporttype_new($name, $externtype);
var_dump($exporttype);

?>
--EXPECTF--
resource(%d) of type (wasm_exporttype_t)
