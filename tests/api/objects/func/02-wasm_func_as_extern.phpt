--TEST--
Func API: wasm_func_as_extern

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
function foo() { var_dump('Hello from PHP user function'); }
$func = wasm_func_new($store, $functype, "foo");
var_dump($extern = wasm_func_as_extern($func));
wasm_func_delete($func);

?>
--EXPECTF--
resource(%d) of type (wasm_extern_t)
