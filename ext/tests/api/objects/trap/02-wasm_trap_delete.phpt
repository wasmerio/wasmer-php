--TEST--
Trap API: wasm_trap_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$trap = wasm_trap_new($store, "Trap message");
var_dump(wasm_trap_delete($trap));

try {
    wasm_trap_delete($trap);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(73) "wasm_trap_delete(): supplied resource is not a valid wasm_trap_t resource"
