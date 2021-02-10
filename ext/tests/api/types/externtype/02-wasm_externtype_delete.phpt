--TEST--
ExternType API: wasm_externtype_delete

--FILE--
<?php

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$externtype = wasm_functype_as_externtype($functype);
var_dump(wasm_externtype_delete($externtype));

try {
    wasm_externtype_delete($externtype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(85) "wasm_externtype_delete(): supplied resource is not a valid wasm_externtype_t resource"
