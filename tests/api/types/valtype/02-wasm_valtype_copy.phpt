--TEST--
ValType API: wasm_valtype_copy

--SKIPIF--
<?php
if (true) print 'skip wasm_valtype_copy not available';

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$valtypeCopy = wasm_valtype_copy($valtype);
var_dump($valtypeCopy);

--EXPECTF--
resource(%d) of type (wasm_valtype_t)
