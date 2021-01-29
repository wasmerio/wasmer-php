--TEST--
Extern API: wasm_extern_as_func

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
function foo() { var_dump('Hello from PHP user function'); }
$func = wasm_func_new($store, $functype, "foo");
$extern = wasm_func_as_extern($func);
var_dump(wasm_extern_as_func($extern));

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
$extern = wasm_global_as_extern($global);
try {
    var_dump(wasm_extern_as_func($extern));
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_func_t)
string(32) "Unable to convert extern to func"
