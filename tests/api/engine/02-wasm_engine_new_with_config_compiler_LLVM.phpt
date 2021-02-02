--TEST--
Engine API: wasm_engine_new_with_config (LLVM)

--FILE--
<?php

$config = wasm_config_new();
wasm_config_set_compiler($config, WASM_COMPILER_LLVM);

try {
    $engine = wasm_engine_new_with_config($config);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
string(53) "Wasmer has not been compiled with the `llvm` feature."
