--TEST--
Engine API: wasm_engine_new_with_config

--FILE--
<?php

$config = wasm_config_new();
$engine = wasm_engine_new_with_config($config);
var_dump($engine);

?>
--EXPECTF--
resource(%d) of type (wasm_engine_t)
