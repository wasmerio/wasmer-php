--TEST--
ValKind API: wasm_valkind_is_num

--FILE--
<?php

$isNum = wasm_valkind_is_num(WASM_I32);
var_dump($isNum);

$isNum = wasm_valkind_is_num(WASM_ANYREF);
var_dump($isNum);

--EXPECTF--
bool(true)
bool(false)
