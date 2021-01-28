--TEST--
Store API: wasm_store_delete

--FILE--
<?php

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
var_dump(wasm_store_delete($store));

try {
    wasm_store_delete($store);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

--EXPECTF--
bool(true)
string(75) "wasm_store_delete(): supplied resource is not a valid wasm_store_t resource"
