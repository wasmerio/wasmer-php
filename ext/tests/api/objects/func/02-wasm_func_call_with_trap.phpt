--TEST--
Func API: wasm_func_call (trap)

--FILE--
<?php

$wat = <<<'WAT'
(module
    (func $func (export "func") unreachable)
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
$exports = wasm_instance_exports($instance);
$func = wasm_extern_as_func($exports[0]);

try {
    wasm_func_call($func, new Wasm\Vec\Val());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
string(11) "unreachable"
