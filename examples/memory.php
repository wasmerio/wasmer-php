<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$instance = new Wasm\Instance(__DIR__ . '/memory.wasm');
$pointer = $instance->return_hello();

$memory = new Wasm\Uint8Array($instance->getMemoryBuffer(), $pointer);

$nth = 0;

while (0 !== $memory[$nth]) {
    echo chr($memory[$nth]);
    ++$nth;
}

echo "\n";
