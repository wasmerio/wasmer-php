--TEST--
ValType API: wasm_valtype_new

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
var_dump($valtype);

--EXPECTF--
resource(%d) of type (wasm_valtype_t)
