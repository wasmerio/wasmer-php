--TEST--
TableType API: wasm_tabletype_limits

--FILE--
<?php

$valtype = wasm_valtype_new(WASM_I32);
$limits = wasm_limits_new(1, 2);
$tabletype = wasm_tabletype_new($valtype, $limits);
var_dump(wasm_tabletype_limits($tabletype));

--EXPECTF--
resource(%d) of type (wasm_limits_t)
