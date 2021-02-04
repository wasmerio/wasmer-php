--TEST--
Func API: wasm_func_call (guest)

--FILE--
<?php

$wat = <<<'WAT'
(module
    (func $run (export "run") (result i32) i32.const 42)
    (func $trap (export "trap") unreachable)
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
$exports = wasm_instance_exports($instance);
$run = wasm_extern_as_func($exports[0]);
$results = wasm_func_call($run, new Wasm\Vec\Val());

var_dump($results);
var_dump(count($results));
var_dump($results[0]);
var_dump(wasm_val_value($results[0]));

try {
    wasm_func_call(wasm_extern_as_func($exports[1]), new Wasm\Vec\Val());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
object(Wasm\Vec\Val)#%d (0) {
}
int(1)
resource(%d) of type (wasm_val_t)
int(42)
string(11) "unreachable"
