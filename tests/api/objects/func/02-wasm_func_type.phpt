--TEST--
Func API: wasm_func_type

--INI--
report_memleaks=Off
--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = wasm_func_new($store, $functype, function () {});
var_dump(wasm_func_type($func));

?>
--EXPECTF--
resource(%d) of type (wasm_functype_t)
