--TEST--
Func API: wasm_func_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = wasm_func_new($store, $functype, function () {});
var_dump(wasm_func_delete($func));

try {
    wasm_func_delete($func);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(73) "wasm_func_delete(): supplied resource is not a valid wasm_func_t resource"
