<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$imports = [
    'add' => function(int $x, int $y): int {
        return $x + $y + 1;
    },
];
$instance = new WASM\Instance(__DIR__ . '/imported_function.wasm', $imports);

var_dump(
    $instance->sum(5, 35) // 42
);
