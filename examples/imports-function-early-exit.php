<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

class ExitCode extends Exception
{
    public function __construct(int $code)
    {
        parent::__construct('Exit code', $code);
    }
}

function earlyExit()
{
    throw new ExitCode(1);
}

// Let's declare the Wasm module.
//
// We are using the text representation of the module here.
$wasmBytes = Wasm\Wat::wasm(<<<'WAT'
    (module
      (type $run_t (func (param i32 i32) (result i32)))
      (type $early_exit_t (func (param) (result)))
      (import "env" "early_exit" (func $early_exit (type $early_exit_t)))
      (func $run (type $run_t) (param $x i32) (param $y i32) (result i32)
        (call $early_exit)
        (i32.add
            local.get $x
            local.get $y))
    (export "run" (func $run)))
WAT);

// Create an Engine
$engine = Wasm\Engine::new();

// Create a Store
$store = Wasm\Store::new($engine);

echo 'Compiling module...'.PHP_EOL;
$module = Wasm\Module::new($store, $wasmBytes);

// Create an import object with the expected function.
$funcType = Wasm\Type\FuncType::new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = Wasm\Func::new($store, $funcType, 'earlyExit');
$extern = $func->asExtern();
$externs = new Wasm\Vec\Extern([$extern->inner()]);

echo 'Instantiating module...'.PHP_EOL;
$instance = Wasm\Instance::new($store, $module, $externs);

// Extracting export...
$exports = $instance->exports();
$run = (new Wasm\Extern($exports[0]))->asFunc();

$firstArg = Wasm\Val::newI32(1);
$secondArg = Wasm\Val::newI32(7);
$args = new Wasm\Vec\Val([$firstArg->inner(), $secondArg->inner()]);

echo 'Calling `run` function...'.PHP_EOL;
try {
    $run($args);

    echo '`run` did not error'.PHP_EOL;

    exit(1);
} catch (ExitCode $exception) {
    echo 'Exited early with: '.$exception->getMessage().' '.$exception->getCode().PHP_EOL;
}
