<?php

namespace WASM\Tests\Units;

use RuntimeException;
use WASM\Instance as SUT;
use WASM\InvocationException;
use WASM\Tests\Suite;

class Instance extends Suite
{
    public function test_constructor_invalid_path()
    {
        $this
            ->given($filePath = '/foo/bar')
            ->exception(
                function () use ($filePath) {
                    new SUT($filePath);
                }
            )
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage("File path to WASM binary `$filePath` does not exist.");
    }
}
