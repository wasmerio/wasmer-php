--TEST--
FuncType API: wasm_functype_delete

--FILE--
<?php

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
var_dump(wasm_functype_delete($functype));

try {
    wasm_functype_delete($functype);
} catch (\Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
bool(true)
string(81) "wasm_functype_delete(): supplied resource is not a valid wasm_functype_t resource"
