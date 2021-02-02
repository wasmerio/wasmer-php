--TEST--
Engine API: wasm_engine_new

--FILE--
<?php

$engine = wasm_engine_new();
var_dump($engine);

?>
--EXPECTF--
resource(%d) of type (wasm_engine_t)
