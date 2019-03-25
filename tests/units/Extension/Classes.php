<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use Wasm\Tests\Suite;

class Classes extends Suite
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_classes()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getClasses())
            ->then
                ->array($result)
                    ->hasSize(1)

            ->when($result = $reflection->getClassNames())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'WasmArrayBuffer',
                    ]);
    }
}
