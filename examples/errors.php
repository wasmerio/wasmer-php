<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

// Let's declare the Wasm module.
//
// We are using the text representation of the module here.
$wasmBytes = Wasm\Wat::wasm(<<<'WAT'
    (module
      (type $do_div_by_zero_t (func (result i32)))
      (func $do_div_by_zero_f (type $do_div_by_zero_t) (result i32)
        i32.const 4
        i32.const 0
        i32.div_s)
      (type $div_by_zero_t (func (result i32)))
      (func $div_by_zero_f (type $div_by_zero_t) (result i32)
        call $do_div_by_zero_f)
      (export "div_by_zero" (func $div_by_zero_f)))
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
$divByZero = (new Wasm\Module\Extern($exports[0]))->asFunc();

echo 'Calling `div_by_zero` function...'.PHP_EOL;

try {
    $result = $divByZero();

    echo '`div_by_zero` did not error'.PHP_EOL;

    exit(1);
} catch (Exception $exception) {
    echo 'Error caught from `div_by_zero`: '.$exception->getMessage().PHP_EOL;
}
