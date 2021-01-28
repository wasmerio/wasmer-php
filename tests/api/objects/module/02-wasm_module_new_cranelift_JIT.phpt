--TEST--
Module API: wasm_module_new (Cranelift, JIT)

--FILE--
<?php

$config = wasm_config_new();
wasm_config_set_compiler($config, WASM_COMPILER_CRANELIFT);
wasm_config_set_engine($config, WASM_ENGINE_JIT);
$engine = wasm_engine_new_with_config($config);
$store = wasm_store_new($engine);
$wasm = wat2wasm('(module)');
$module = wasm_module_new($store, $wasm);
var_dump($module);

--EXPECTF--
resource(%d) of type (wasm_module_t)
