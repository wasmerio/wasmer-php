--TEST--
FuncType API: wasm_functype_new

--FILE--
<?php

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
var_dump($functype);

--EXPECTF--
resource(%d) of type (wasm_functype_t)
