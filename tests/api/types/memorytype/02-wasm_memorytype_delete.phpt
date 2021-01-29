--TEST--
MemoryType API: wasm_memorytype_delete

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
var_dump(wasm_memorytype_delete($memorytype));

try {
    wasm_memorytype_delete($memorytype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(85) "wasm_memorytype_delete(): supplied resource is not a valid wasm_memorytype_t resource"
