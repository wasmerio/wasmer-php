<?php

$php = getenv('PHP_EXECUTABLE') ?: 'php';

$runner->setPhpPath($php.' -dextension='.__DIR__.'/ext/modules/wasm.so');
$runner->addTestsFromDirectory('tests');
