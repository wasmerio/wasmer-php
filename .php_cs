<?php

$finder = PhpCsFixer\Finder::create()
    ->notName('wasmer_vec.stub.php')
    ->in('src')
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR2' => true,
        'strict_param' => true,
        'phpdoc_align' => true,
    ])
    ->setFinder($finder)
;
