--TEST--
Engine API: wasm_engine_new_with_config (Singlepass)

--FILE--
<?php

$config = wasm_config_new();
wasm_config_set_compiler($config, WASM_COMPILER_SINGLEPASS);

try {
    $engine = wasm_engine_new_with_config($config);
} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
string(59) "Wasmer has not been compiled with the `singlepass` feature."
