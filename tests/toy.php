<?php

require_once dirname(__DIR__) . '/lib/WASM.php';

$instance = new WASM\Instance(__DIR__ . '/toy.wasm');
$result = $instance->sum(5, 37);

var_dump($result);
