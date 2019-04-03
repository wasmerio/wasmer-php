<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

const KEY = 'testing';
const CACHE_DIRECTORY = __DIR__ . '/module_caching';

$cache = new Wasm\Cache\Filesystem(CACHE_DIRECTORY);

if ($cache->has(KEY)) {
    $module = $cache->get(KEY);
} else {
    $module = new Wasm\Module(__DIR__ . '/simple.wasm');
    $cache->set(KEY, $module);
}

$instance = $module->instantiate();
var_dump($instance->sum(1, 2));
