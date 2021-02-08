--TEST--
Wasmer: wasmer_version

--FILE--
<?php

var_dump(wasmer_version());

?>
--EXPECTF--
string(5) "%d.%d.%d"
