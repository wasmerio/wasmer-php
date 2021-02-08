--TEST--
Wasmer PHP

--FILE--
<?php

var_dump(extension_loaded('wasm'));

?>
--EXPECTF--
bool(true)
