<?php

echo 'Initializing...' . PHP_EOL;
$engine = wasm_engine_new();
$store = wasm_store_new($engine);

echo 'Loading WAT...' . PHP_EOL;
$wat = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . basename(__FILE__, '.php') . '.wat');

echo 'Loading binary...' . PHP_EOL;
$wasm = wat2wasm($wat);

echo 'Compiling module...' . PHP_EOL;
$module = wasm_module_new($store, $wasm);

echo 'Creating callback...' . PHP_EOL;
function print_callback(int $i32): int {
    echo 'Calling back...' . PHP_EOL . '> ' . $i32 . PHP_EOL;

    assert($i32 === 7);

    return $i32;
}

function closure(): int {
    echo 'Calling back closure...' . PHP_EOL;

    return 42;
}

$printType = wasm_functype_new(
    new Wasm\Vec\ValType([wasm_valtype_new(WASM_I32)]),
    new Wasm\Vec\ValType([wasm_valtype_new(WASM_I32)])
);
$print = wasm_func_new($store, $printType, 'print_callback');
wasm_functype_delete($printType);

$closureType = wasm_functype_new(
    new Wasm\Vec\ValType(),
    new Wasm\Vec\ValType([wasm_valtype_new(WASM_I32)])
);
$closure = wasm_func_new($store, $closureType, 'closure');
wasm_functype_delete($closureType);

echo 'Instantiating module...' . PHP_EOL;
$externs = new Wasm\Vec\Extern([wasm_func_as_extern($print), wasm_func_as_extern($closure)]);
$instance = wasm_instance_new($store, $module, $externs);

wasm_func_delete($print);
wasm_func_delete($closure);

echo 'Extracting export...' . PHP_EOL;
$exports = wasm_instance_exports($instance);
$run = wasm_extern_as_func($exports[0]);

wasm_module_delete($module);
wasm_instance_delete($instance);

echo 'Calling export...' . PHP_EOL;
$args = new Wasm\Vec\Val([
    wasm_val_i32(3),
    wasm_val_i32(4),
]);

$results = wasm_func_call($run, $args);

echo 'Printing result...' . PHP_EOL;
$result = wasm_val_value($results[0]);
echo '> ' . $result . PHP_EOL;
assert($result === 49);

echo 'Shutting down...' . PHP_EOL;
wasm_store_delete($store);
wasm_engine_delete($engine);