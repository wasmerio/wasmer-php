<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use StdClass;
use Wasm\Tests\Suite;

class Dependencies extends Suite
{
    public function getTestedClassName()
    {
        return StdClass::class;
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_dependencies()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getDependencies())
            ->then
                ->array($result)
                    ->isEmpty();
    }
}
