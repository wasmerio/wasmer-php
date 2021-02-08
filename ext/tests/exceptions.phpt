--TEST--
Wasmer PHP - Exceptions

--FILE--
<?php

var_dump(class_exists(Wasm\Exception\RuntimeException::class));
$exception = new Wasm\Exception\RuntimeException();
var_dump($exception instanceof RuntimeException);

var_dump(class_exists(Wasm\Exception\InstantiationException::class));
$exception = new Wasm\Exception\InstantiationException();
var_dump($exception instanceof Wasm\Exception\RuntimeException);

var_dump(class_exists(Wasm\Exception\OutOfBoundsException::class));
$exception = new Wasm\Exception\OutOfBoundsException();
var_dump($exception instanceof Wasm\Exception\RuntimeException);

?>
--EXPECTF--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
