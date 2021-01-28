--TEST--
FuncType API: wasm_functype_copy

--FILE--
<?php

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$functypeCopy = wasm_functype_copy($functype);
var_dump($functypeCopy);

--EXPECTF--
resource(%d) of type (wasm_functype_t)
