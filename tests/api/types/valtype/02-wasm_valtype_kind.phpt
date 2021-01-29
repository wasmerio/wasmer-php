--TEST--
ValType API: wasm_valtype_kind

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$kind = wasm_valtype_kind($valtype);
var_dump($kind == WASM_I32);

?>
--EXPECTF--
bool(true)
