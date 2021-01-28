--TEST--
MemoryType API: wasm_memorytype_as_externtype

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$externtype = wasm_memorytype_as_externtype($memorytype);
var_dump($externtype);

--EXPECTF--
resource(%d) of type (wasm_externtype_t)
