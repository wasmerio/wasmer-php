--TEST--
Engine API: wasm_engine_new_with_config (Cranelift, Object File)

--FILE--
<?php

$config = wasm_config_new();
wasm_config_set_compiler($config, WASM_COMPILER_CRANELIFT);
wasm_config_set_engine($config, WASM_ENGINE_OBJECT_FILE);
$engine = wasm_engine_new_with_config($config);
var_dump($engine);

?>
--EXPECTF--
resource(%d) of type (wasm_engine_t)
