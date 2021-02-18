--TEST--
ImportType API: wasm_importtype_new

--FILE--
<?php

$module = "module";
$name = "name";
$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$externtype = wasm_globaltype_as_externtype($globaltype);
$importtype = wasm_importtype_new($module, $name, $externtype);
var_dump($importtype);

?>
--EXPECTF--
resource(%d) of type (wasm_importtype_t)
