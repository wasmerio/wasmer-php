--TEST--
Wasmer: wasmer_version_minor

--FILE--
<?php

var_dump(wasmer_version_minor());

?>
--EXPECTF--
int(%d)
