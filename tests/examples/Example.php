<?php

declare(strict_types=1);

namespace Wasm\Examples;

use PHPUnit\Framework\TestCase;

abstract class Example extends TestCase
{
    protected function module(?string $extension = null): string
    {
        $class = str_replace(__NAMESPACE__.'\\', '', get_called_class());

        return __DIR__.DIRECTORY_SEPARATOR.strtolower($class).'.'.($extension ?? 'wat');
    }
}
