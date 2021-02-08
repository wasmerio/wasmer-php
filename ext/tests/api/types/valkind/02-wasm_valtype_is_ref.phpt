--TEST--
ValKind API: wasm_valkind_is_ref

--FILE--
<?php

$isRef = wasm_valkind_is_ref(WASM_ANYREF);
var_dump($isRef);

$isRef = wasm_valkind_is_ref(WASM_I32);
var_dump($isRef);

?>
--EXPECTF--
bool(true)
bool(false)
