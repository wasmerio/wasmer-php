--TEST--
Module API: wasm_module_new (Cranelift, Object File)

--FILE--
<?php

$config = wasm_config_new();
wasm_config_set_compiler($config, WASM_COMPILER_CRANELIFT);
wasm_config_set_engine($config, WASM_ENGINE_OBJECT_FILE);
$engine = wasm_engine_new_with_config($config);
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');

try {
    $module = wasm_module_new($store, $wasm);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
string(121) "Compilation error: The `ObjectFileEngine` is operating in headless mode, so it can only execute already compiled Modules."
