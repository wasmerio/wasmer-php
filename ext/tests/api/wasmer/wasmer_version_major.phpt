--TEST--
Wasmer: wasmer_version_major

--FILE--
<?php

var_dump(wasmer_version_major());

?>
--EXPECTF--
int(%d)
