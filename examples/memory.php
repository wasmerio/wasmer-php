<?php

declare(strict_types = 1);

$bytes = wasm_fetch_bytes(__DIR__ . '/memory.wasm');
$instance = wasm_new_instance($bytes);
$pointer = wasm_invoke_function($instance, 'return_hello', []);

$memory = wasm_get_memory_buffer($instance);
$view = new WasmUint8Array($memory, $pointer);

$nth = 0;

while (0 !== $view[$nth]) {
    echo chr($view[$nth]);
    ++$nth;
}

echo "\n";
