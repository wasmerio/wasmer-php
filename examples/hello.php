<?php declare(strict_types=1);

echo 'Initializing...' . PHP_EOL;
$engine = wasm_engine_new();
$store = wasm_store_new($engine);

echo 'Loading binary...' . PHP_EOL;
$wasm = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'hello.wasm');

echo 'Compiling module...' . PHP_EOL;
$module = wasm_module_new($store, $wasm);

echo 'Creating callback...' . PHP_EOL;
function hello_callback() {
    echo 'Calling back...' . PHP_EOL;
    echo '> Hello World!' . PHP_EOL;

    return null;
}

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
$func = wasm_func_new($store, $functype, 'hello_callback');
wasm_functype_delete($functype);

echo 'Instantiating module...' . PHP_EOL;
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
$instance = wasm_instance_new($store, $module, $externs);

wasm_func_delete($func);

echo 'Extracting export...' . PHP_EOL;
$exports = wasm_instance_exports($instance);
$run = wasm_extern_as_func($exports[0]);

wasm_module_delete($module);
wasm_instance_delete($instance);

echo 'Calling export...' . PHP_EOL;
$args = new Wasm\Vec\Val();
$results = wasm_func_call($run, $args);

echo 'Shutting down...' . PHP_EOL;
wasm_store_delete($store);
wasm_engine_delete($engine);
