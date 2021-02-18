--TEST--
Trap API: wasm_trap_new

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
var_dump(wasm_trap_new($store, "Trap message"));

?>
--EXPECTF--
resource(%d) of type (wasm_trap_t)
