--TEST--
Wasmer PHP

--FILE--
<?php

var_dump(extension_loaded('wasmer'));

?>
--EXPECTF--
bool(true)
