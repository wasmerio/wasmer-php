<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use Wasm\Tests\Suite;

class Name extends Suite
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_name()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getName())
            ->then
                ->string($result)
                    ->isEqualTo('wasm');
    }
}
