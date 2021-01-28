--TEST--
Instance API: wasm_instance_new (trap)

--FILE--
<?php

$wat = <<<'WAT'
(module
  (start $run)
  (func $run unreachable)
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);

try {
    $instance = wasm_instance_new($store, $module, new Wasm\Vec\Extern());
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>

--EXPECTF--
string(11) "unreachable"
