<?php

declare(strict_types = 1);

$bytes = wasm_fetch_bytes(__DIR__ . '/imported_function.wasm');

$imports = [
    'env' => [
        'add' => function(int $x, int $y): int {
            return $x + $y + 1;
        },
    ],
];

$instance = wasm_new_instance($bytes, $imports);

echo 'instance = '; var_dump($instance);
echo 'last error = '; var_dump(wasm_get_last_error());
echo 'result = '; var_dump(
    wasm_invoke_function(
        $instance,
        'sum',
        [1, 2]
    )
);

/*
$instance = new WASM\Instance(__DIR__ . '/imported_function.wasm', $imports);

var_dump(
    $instance->sum(5, 35) // 42
);

*/
