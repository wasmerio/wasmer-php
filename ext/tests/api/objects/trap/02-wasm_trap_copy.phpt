--TEST--
Trap API: wasm_trap_copy

--SKIPIF--
<?php

if (true) print 'skip wasm_trap_copy not available';

?>
--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$trap = wasm_trap_new($store, "Trap message");
var_dump(wasm_trap_copy($trap));

?>
--EXPECTF--
resource(%d) of type (wasm_trap_t)
