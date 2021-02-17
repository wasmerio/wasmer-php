<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

// Let's declare the Wasm module.
//
// We are using the text representation of the module here.
$wasmBytes = Wasm\Wat::wasm(<<<'WAT'
    (module
      (type $sum_t (func (param i32 i32) (result i32)))
      (func $sum_f (type $sum_t) (param $x i32) (param $y i32) (result i32)
        local.get $x
        local.get $y
        i32.add)
      (export "sum" (func $sum_f)))
WAT);

// Create an Engine
$engine = Wasm\Engine::new();

// Create a Store
$store = Wasm\Store::new($engine);

echo 'Compiling module...'.PHP_EOL;
$module = Wasm\Module::new($store, $wasmBytes);

echo 'Instantiating module...'.PHP_EOL;
$instance = Wasm\Module\Instance::new($store, $module);

// Extracting export...
$exports = $instance->exports();
$sum = (new Wasm\Module\Extern($exports[0]))->asFunc();

$firstArg = Wasm\Module\Val::newI32(1);
$secondArg = Wasm\Module\Val::newI32(2);
$args = new Wasm\Vec\Val([$firstArg->inner(), $secondArg->inner()]);

echo 'Calling `sum` function...'.PHP_EOL;
$result = $sum($args);

echo 'Results of `sum`: '.((new Wasm\Module\Val($result[0]))->value()).PHP_EOL;
