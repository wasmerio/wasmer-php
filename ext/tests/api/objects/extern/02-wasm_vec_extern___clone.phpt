--TEST--
Extern API: Wasm\Vec\Extern (clone)

--FILE--
<?php

$vec = new Wasm\Vec\Extern();

try {
    $clone = clone $vec;
} catch (Error $e) {
    var_dump($e->getMessage());
}

?>
--EXPECTF--
string(62) "Trying to clone an uncloneable object of class Wasm\Vec\Extern"
