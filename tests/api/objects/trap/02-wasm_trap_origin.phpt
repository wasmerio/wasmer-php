--TEST--
Trap API: wasm_trap_origin

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$trap = wasm_trap_new($store, "Trap message");
var_dump(wasm_trap_origin($trap));

?>
--EXPECTF--
NULL
