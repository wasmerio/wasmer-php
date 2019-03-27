<?php

declare(strict_types = 1);

$bytes = wasm_fetch_bytes(__DIR__ . '/memory.wasm');
$instance = wasm_new_instance($bytes);
$result = wasm_invoke_function($instance, 'return_hello', []);

$memory = wasm_get_memory_buffer($instance);
$view = new WasmInt8Array($memory);

$nth = $result;

while (0 !== $view[$nth]) {
    echo chr($view[$nth]);
    ++$nth;
}
