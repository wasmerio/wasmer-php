--TEST--
GlobalType API: wasm_globaltype_copy

--SKIPIF--
<?php
if (true) print 'skip wasm_globaltype_copy not available';

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$globaltype = wasm_globaltype_new($valtype, WASM_CONST);
$globaltypeCopy = wasm_globaltype_copy($globaltype);
var_dump($globaltypeCopy);

?>
--EXPECTF--
resource(%d) of type (wasm_globaltype_t)
