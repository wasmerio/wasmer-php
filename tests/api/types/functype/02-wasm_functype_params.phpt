--TEST--
FuncType API: wasm_functype_params

--FILE--
<?php
$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
var_dump(wasm_functype_params($functype));

--EXPECTF--
object(Wasm\Vec\ValType)#%d (%d) {
}
