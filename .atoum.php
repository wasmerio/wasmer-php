<?php

$php = getenv('PHP_EXECUTABLE') ?: 'php';

$script->enableBranchAndPathCoverage();
$runner->setPhpPath($php.' -dextension='.__DIR__.'/ext/modules/wasm.so -dzend_extension=/Users/jubianchi.wasm/repositories/php/php-src/php-build-debug/lib/php/extensions/debug-non-zts-20200930/xdebug.so -dxdebug.mode=coverage');
$runner->addTestsFromDirectory('tests');
