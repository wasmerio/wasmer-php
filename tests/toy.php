<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$imports = [
    'add' => function(int $x, int $y): int {
        return $x + $y;
    },
];
$instance = new WASM\Instance(__DIR__ . '/toy.wasm', $imports);
var_dump(
    $instance->sum(40, 1),
    $instance->sum(1, 1)
);
