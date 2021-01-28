--TEST--
MemoryType API: wasm_memorytype_copy

--SKIPIF--
<?php
if (true) print 'skip wasm_memorytype_copy not available';

--FILE--
<?php

$limits = wasm_limits_new(1, 2);
$memorytype = wasm_memorytype_new($limits);
$memorytypeCopy = wasm_memorytype_copy($memorytype);
var_dump($memorytypeCopy);

--EXPECTF--
resource(%d) of type (wasm_memorytype_t)
