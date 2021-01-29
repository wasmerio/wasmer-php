--TEST--
Store API: wasm_store_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
var_dump($store);

?>
--EXPECTF--
resource(%d) of type (wasm_store_t)
