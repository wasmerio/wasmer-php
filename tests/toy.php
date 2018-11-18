<?php

require_once dirname(__DIR__) . '/lib/WASM.php';

$instance = new WASM\Instance('./tests/toy.wasm');
$instance->sum(5, 37);
