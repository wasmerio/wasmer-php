--TEST--
Extern API: wasm_extern_as_global

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
$extern = wasm_global_as_extern($global);
var_dump(wasm_extern_as_global($extern));

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
function foo() { var_dump('Hello from PHP user function'); }
$func = wasm_func_new($store, $functype, "foo");
$extern = wasm_func_as_extern($func);
try {
    var_dump(wasm_extern_as_global($extern));
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
resource(%d) of type (wasm_global_t)
string(34) "Unable to convert extern to global"
