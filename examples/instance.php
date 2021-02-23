<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

// Let's declare the Wasm module.
//
// We are using the text representation of the module here.
$wasmBytes = Wasm\Wat::wasm(<<<'WAT'
    (module
      (type $add_one_t (func (param i32) (result i32)))
      (func $add_one_f (type $add_one_t) (param $value i32) (result i32)
        local.get $value
        i32.const 1
        i32.add)
      (export "add_one" (func $add_one_f)))
WAT);

// Create an Engine
$engine = Wasm\Engine::new();

// Create a Store
$store = Wasm\Store::new($engine);

echo 'Compiling module...'.PHP_EOL;
$module = Wasm\Module::new($store, $wasmBytes);

echo 'Instantiating module...'.PHP_EOL;
$instance = Wasm\Instance::new($store, $module);

// Extracting export...
$exports = $instance->exports();
$addOne = (new Wasm\Extern($exports[0]))->asFunc();

$arg = Wasm\Val::newI32(1);
$args = new Wasm\Vec\Val([$arg->inner()]);

echo 'Calling `add_one` function...'.PHP_EOL;
$result = $addOne($args);

echo 'Results of `add_one`: '.((new Wasm\Val($result[0]))->value()).PHP_EOL;
