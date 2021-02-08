--TEST--
Engine API: wasm_engine_delete

--FILE--
<?php

$engine = wasm_engine_new();
var_dump(wasm_engine_delete($engine));

try {
    wasm_engine_delete($engine);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(77) "wasm_engine_delete(): supplied resource is not a valid wasm_engine_t resource"
