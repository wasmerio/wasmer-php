--TEST--
Config API: wasm_config_set_engine

--FILE--
<?php

$config = wasm_config_new();
var_dump(wasm_config_set_engine($config, WASM_ENGINE_JIT));
var_dump(wasm_config_set_engine($config, WASM_ENGINE_NATIVE));
var_dump(wasm_config_set_engine($config, WASM_ENGINE_OBJECT_FILE));

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
