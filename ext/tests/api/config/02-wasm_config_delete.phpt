--TEST--
Config API: wasm_config_delete

--FILE--
<?php

$config = wasm_config_new();
var_dump(wasm_config_delete($config));

try {
    wasm_config_delete($config);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(77) "wasm_config_delete(): supplied resource is not a valid wasm_config_t resource"
