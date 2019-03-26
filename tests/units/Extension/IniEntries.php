<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ReflectionExtension;
use StdClass;
use Wasm\Tests\Suite;

class IniEntries extends Suite
{
    public function getTestedClassName()
    {
        return StdClass::class;
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_reflection_ini_entries()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getINIEntries())
            ->then
                ->array($result)
                    ->isEmpty();
    }
}
