--TEST--
TableType API: wasm_tabletype_copy

--SKIPIF--
<?php
if (true) print 'skip wasm_tabletype_copy not available';

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$limits = wasm_limits_new(1, 2);
$tabletype = wasm_tabletype_new($valtype, $limits);
$tabletypeCopy = wasm_tabletype_copy($tabletype);
var_dump($tabletypeCopy);

?>
--EXPECTF--
resource(%d) of type (wasm_tabletype_t)
