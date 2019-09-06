<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Declare the imports. Each callable has a name (here `add`), within a
// namespace (here `env`).
$imports = [
    'env' => [
        'add' => function(int $x, int $y): int {
            return $x + $y + 1;
        },
    ],
];

// Instantiate the WebAssembly module with the imported functions.
$instance = new Wasm\Instance(__DIR__ . '/imported_function.wasm', $imports);

// Invoke the exported function as usual.
var_dump(
    $instance->sum(5, 35) // 42
);
