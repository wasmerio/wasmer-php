<?php

declare(strict_types=1);

use Wasm;

require_once __DIR__ . '/../vendor/autoload.php';

echo 'Initializing...' . PHP_EOL;
$engine = Wasm\Engine::new();
$store = Wasm\Store::new($engine);

echo 'Loading binary...' . PHP_EOL;
$wasm = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.wasm');

echo 'Compiling module...' . PHP_EOL;
$module = Wasm\Module::new($store, $wasm);

echo 'Creating callback...' . PHP_EOL;
function hello_callback() {
    echo 'Calling back...' . PHP_EOL;
    echo '> Hello World!' . PHP_EOL;

    return null;
}

$functype = Wasm\Functype::new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = Wasm\Module\Func::new($store, $functype, 'hello_callback');

echo 'Instantiating module...' . PHP_EOL;
$extern = $func->asExtern();
$externs = new Wasm\Vec\Extern([$extern->inner()]);
$instance = Wasm\Module\Instance::new($store, $module, $externs);

echo 'Extracting export...' . PHP_EOL;
$exports = $instance->exports();
$run = $exports[0]->asFunc();

echo 'Calling export...' . PHP_EOL;
$args = new Wasm\Vec\Val();
$results = $run($args);
