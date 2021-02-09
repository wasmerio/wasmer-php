<?php

$finder = PhpCsFixer\Finder::create()
    ->notName('wasmer_vec.stub.php')
    ->in('ext/src')
    ->in('src')
;

$config = new PhpCsFixer\Config();

return $config->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'strict_param' => true,
        'phpdoc_align' => true,
    ])
    ->setFinder($finder)
;
