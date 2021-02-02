--TEST--
MemoryType API: wasm_memorytype_new

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
var_dump($memorytype);

?>
--EXPECTF--
resource(%d) of type (wasm_memorytype_t)
