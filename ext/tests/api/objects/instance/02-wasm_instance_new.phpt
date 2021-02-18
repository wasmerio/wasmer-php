--TEST--
Instance API: wasm_instance_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
$instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
var_dump($instance);

$wat = <<<'WAT'
(module
  (func $print (import "" "print") (param i32) (result i32))
)
WAT;
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);

try {
    $instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
} catch (Wasm\Exception\InstantiationException $e) {
    var_dump($e->getMessage());
}


?>
--EXPECTF--
resource(%d) of type (wasm_instance_t)
string(115) "Error while importing ""."print": unknown import. Expected Function(FunctionType { params: [I32], results: [I32] })"
