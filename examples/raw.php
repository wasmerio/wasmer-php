<?php

$wasm_bytes = wasm_read_bytes('./simple.wasm');
$wasm_instance = wasm_new_instance($wasm_bytes);
$result = wasm_invoke_function(
    $wasm_instance,
    'sum',
    [
        wasm_value(WASM_TYPE_I32, 1),
        wasm_value(WASM_TYPE_I32, 2)
    ]
);
var_dump($result);
