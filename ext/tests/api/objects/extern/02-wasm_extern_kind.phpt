--TEST--
Extern API: wasm_extern_kind

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);

$globaltype = wasm_globaltype_new(wasm_valtype_new(WASM_I32), WASM_CONST);
$global = wasm_global_new($store, $globaltype, wasm_val_i32(1));
$globalExtern = wasm_global_as_extern($global);
var_dump(wasm_extern_kind($globalExtern) === WASM_EXTERN_GLOBAL);

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
function foo() { var_dump('Hello from PHP user function'); }
$func = wasm_func_new($store, $functype, "foo");
$funcExtern = wasm_func_as_extern($func);
var_dump(wasm_extern_kind($funcExtern) === WASM_EXTERN_FUNC);

// TODO(jubianchi): Add tests for memory and table

?>
--EXPECTF--
bool(true)
bool(true)
