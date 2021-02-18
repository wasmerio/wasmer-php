--TEST--
Wasmer: wasmer_version_patch

--FILE--
<?php

var_dump(wasmer_version_patch());

?>
--EXPECTF--
int(%d)
