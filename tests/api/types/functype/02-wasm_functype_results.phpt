--TEST--
FuncType API: wasm_functype_results

--FILE--
<?php
$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
var_dump(wasm_functype_results($functype));

--EXPECTF--
object(Wasm\Vec\ValType)#%d (%d) {
}
