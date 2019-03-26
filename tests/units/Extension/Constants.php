<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use StdClass;
use Wasm\Tests\Suite;

class Constants extends Suite
{
    public function getTestedClassName()
    {
        return StdClass::class;
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_constants()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getConstants())
            ->then
                ->array($result)
                    ->isEqualTo([
                        'WASM_TYPE_I32' => 0,
                        'WASM_TYPE_I64' => 1,
                        'WASM_TYPE_F32' => 2,
                        'WASM_TYPE_F64' => 3,
                    ]);
    }
}
