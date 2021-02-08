--TEST--
Global API: wasm_global_set (guest)

--FILE--
<?php

$wat = <<<'WAT'
(module
    (global $var (export "var") (mut i32) (i32.const 1))
    (global $const (export "const") i32 (i32.const 2))
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
$exports = wasm_instance_exports($instance);
$var = wasm_extern_as_global($exports[0]);
$const = wasm_extern_as_global($exports[1]);
wasm_global_set($var, wasm_val_i32(42));

var_dump(wasm_val_value(wasm_global_get($var)));

try {
    wasm_global_set($const, wasm_val_i32(42));
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
int(42)
string(50) "RuntimeError: Attempted to set an immutable global"
