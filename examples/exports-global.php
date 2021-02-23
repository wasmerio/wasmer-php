<?php

declare(strict_types=1);

use Wasm\Type\GlobalType;

require_once __DIR__.'/../vendor/autoload.php';

// Let's declare the Wasm module.
//
// We are using the text representation of the module here.
$wasmBytes = Wasm\Wat::wasm(<<<'WAT'
    (module
      (global $one (export "one") f32 (f32.const 1))
      (global $some (export "some") (mut f32) (f32.const 0))

      (func (export "get_one") (result f32) (global.get $one))
      (func (export "get_some") (result f32) (global.get $some))

      (func (export "set_some") (param f32) (global.set $some (local.get 0))))
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
$one = (new Wasm\Extern($exports[0]))->asGlobal();
$some = (new Wasm\Extern($exports[1]))->asGlobal();

echo 'Getting globals types information...'.PHP_EOL;
$oneType = $one->type();
$someType = $some->type();

echo '`one` type: '.(GlobalType::MUTABILITY_CONST === $oneType->mutability() ? 'CONST' : 'VAR').' '.$oneType->content()->kind().PHP_EOL;
echo '`some` type: '.(GlobalType::MUTABILITY_CONST === $someType->mutability() ? 'CONST' : 'VAR').' '.$someType->content()->kind().PHP_EOL;

echo 'Getting global values...'.PHP_EOL;
$getOne = (new Wasm\Extern($exports[2]))->asFunc();

$results = $getOne();
$oneValue = (new Wasm\Val($results[0]))->value();

$someValue = $some->get()->value();

echo '`one` value: '.$oneValue.PHP_EOL;
echo '`some` value: '.$someValue.PHP_EOL;

echo 'Setting global values...'.PHP_EOL;

try {
    $one->set(42.0);

    echo 'Setting value to `one` did not error'.PHP_EOL;

    exit(1);
} catch (\Wasm\Exception\RuntimeException $exception) {
    assert('RuntimeError: Attempted to set an immutable global' === $exception->getMessage());
}

$results = $getOne();
$oneValue = (new Wasm\Val($results[0]))->value();

echo '`one` value: '.$oneValue.PHP_EOL;

$setSome = (new Wasm\Extern($exports[4]))->asFunc();

$arg = Wasm\Val::newF32(21.0);
$args = new Wasm\Vec\Val([$arg->inner()]);
$setSome($args);

$someValue = $some->get()->value();

echo '`some` value after `set_some`: '.$someValue.PHP_EOL;

$some->set(42.0);
$someValue = $some->get()->value();

echo '`some` value after `set`: '.$someValue.PHP_EOL;
