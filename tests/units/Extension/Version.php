<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use StdClass;
use Wasm\Tests\Suite;

class Version extends Suite
{
    public function getTestedClassName()
    {
        return StdClass::class;
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_version()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getVersion())
            ->then
                ->string($result)
                    ->isEqualTo('0.2.0');
    }
}
