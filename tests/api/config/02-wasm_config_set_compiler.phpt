--TEST--
Config API: wasm_config_set_compiler

--FILE--
<?php

$config = wasm_config_new();
var_dump(wasm_config_set_compiler($config, WASM_COMPILER_CRANELIFT));
var_dump(wasm_config_set_compiler($config, WASM_COMPILER_LLVM));
var_dump(wasm_config_set_compiler($config, WASM_COMPILER_SINGLEPASS));

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
