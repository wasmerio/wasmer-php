--TEST--
MemoryType API: wasm_memorytype_limits

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$limits = wasm_memorytype_limits($memorytype);
var_dump($limits);
var_dump(wasm_limits_min($limits));
var_dump(wasm_limits_max($limits));

?>
--EXPECTF--
resource(%d) of type (wasm_limits_t)
int(1)
int(2)
