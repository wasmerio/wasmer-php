<?php

declare(strict_types=1);

echo 'Initializing...'.PHP_EOL;
$engine = wasm_engine_new();
$store = wasm_store_new($engine);

echo 'Loading binary...'.PHP_EOL;
$wat = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'memory.wat');

echo 'Loading binary...'.PHP_EOL;
$wasm = wat2wasm($wat);

echo 'Compiling module...'.PHP_EOL;
$module = wasm_module_new($store, $wasm);

echo 'Instantiating module...'.PHP_EOL;
$externs = new Wasm\Vec\Extern();
$instance = wasm_instance_new($store, $module, $externs);

echo 'Extracting export...'.PHP_EOL;
$exports = wasm_instance_exports($instance);
$getAt = wasm_extern_as_func($exports[0]);
$setAt = wasm_extern_as_func($exports[1]);
$memSize = wasm_extern_as_func($exports[2]);
$memory = wasm_extern_as_memory($exports[3]);

wasm_module_delete($module);
wasm_instance_delete($instance);

echo 'Querying memory size...'.PHP_EOL;
assert(1 === wasm_memory_size($memory));
assert(65536 === wasm_memory_data_size($memory));

$result = wasm_func_call($memSize, new Wasm\Vec\Val());
assert(1 === wasm_val_value($result[0]));

echo 'Growing memory...'.PHP_EOL;
wasm_memory_grow($memory, 2);
assert(3 === wasm_memory_size($memory));
assert(wasm_memory_data_size($memory) === 65536 * 3);

$memAddr = 0;
$val = 5;
wasm_func_call($setAt, new Wasm\Vec\Val([
    wasm_val_i32($memAddr),
    wasm_val_i32($val),
]));

$result = wasm_func_call($getAt, new Wasm\Vec\Val([wasm_val_i32($memAddr)]));
assert(wasm_val_value($result[0]) === $val);

$view = wasm_memory_data($memory);
assert($view->getI32($memAddr) == wasm_val_value($result[0]));

echo 'Shutting down...'.PHP_EOL;
wasm_store_delete($store);
wasm_engine_delete($engine);
