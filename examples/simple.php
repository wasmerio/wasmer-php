<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$instance = new Wasm\Instance(__DIR__ . '/simple.wasm');

var_dump(
    $instance->sum(5, 37) // 42!
);
