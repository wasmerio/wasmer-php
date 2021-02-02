--TEST--
Global API: wasm_global_get (guest)

--FILE--
<?php

$wat = <<<'WAT'
(module
    (global $global (export "global") i32 (i32.const 42))
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
$exports = wasm_instance_exports($instance);
$global = wasm_extern_as_global($exports[0]);
$value = wasm_global_get($global);

var_dump($value);
var_dump(wasm_val_value($value));

?>
--EXPECTF--
resource(%d) of type (wasm_val_t)
int(42)
