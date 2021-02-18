--TEST--
MemoryType API: wasm_memorytype_limits

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
var_dump(wasm_memorytype_limits($memorytype));

?>
--EXPECTF--
resource(%d) of type (wasm_limits_t)
