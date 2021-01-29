--TEST--
WAT: wat2wasm

--FILE--
<?php

$wat = <<<'WAT'
(module
  (type $add_one_t (func (param i32) (result i32)))
  (func $add_one_f (type $add_one_t) (param $value i32) (result i32)
    local.get $value
    i32.const 1
    i32.add)
  (export "add_one" (func $add_one_f)))
WAT;
$wasm = wat2wasm($wat);

?>
--EXPECTF--
