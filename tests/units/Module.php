<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units;

use RuntimeException;
use Serializable;
use Wasm as LUT;
use Wasm\Module as SUT;
use Wasm\Tests\Suite;

class Module extends Suite
{
    const FILE_PATH = __DIR__ . '/tests.wasm';

    public function test_constructor_invalid_path()
    {
        $this
            ->given($filePath = __DIR__ . '/foo')
            ->exception(
                function () use ($filePath) {
                    new SUT($filePath);
                }
            )
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage("File path to Wasm binary `$filePath` does not exist.");
    }

    public function test_constructor_cannot_read_the_module()
    {
        $this
            ->given(
                $filePath = __DIR__ . '/foo',
                $this->function->file_exists = true,
                $this->function->is_readable = true
            )
            ->exception(
                function () use ($filePath) {
                    new SUT($filePath);
                }
            )
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage("Bytes in `$filePath` are invalid.");
    }

    public function test_constructor_invalid_bytes()
    {
        $this
            ->given($filePath = __DIR__ . '/invalid.wasm')
            ->exception(
                function () use ($filePath) {
                    new SUT($filePath);
                }
            )
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage("Bytes in `$filePath` are invalid.");
    }

    public function test_constructor_invalid_compilation()
    {
        $this
            ->given(
                $filePath = __DIR__ . '/empty.wasm',
                $this->function->wasm_validate = true
            )
            ->exception(
                function () use ($filePath) {
                    new SUT($filePath);
                }
            )
                ->isInstanceOf(RuntimeException::class)
                ->hasMessage(
                    "An error happened while compiling the module `$filePath`:\n" .
                    "    Validation error \"Unexpected EOF\""
                );
    }

    public function test_instantiate()
    {
        $this
            ->given($module = new SUT(static::FILE_PATH))
            ->when($result = $module->instantiate())
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Instance::class)
                ->integer($result->sum(1, 2))
                    ->isEqualTo(3);
    }

    public function test_into_resource()
    {
        $this
            ->given($module = new SUT(static::FILE_PATH))
            ->when($result = $module->intoResource())
            ->then
                ->resource($result)
                    ->isOfType('wasm_module');
    }

    public function test_serializable()
    {
        $this
            ->when($module = new SUT(static::FILE_PATH))
            ->then
                ->object($module)
                    ->isInstanceOf(Serializable::class);
    }

    public function test_serialize()
    {
        $this
            ->given($module = new SUT(static::FILE_PATH))
            ->when($result = serialize($module))
            ->then
                ->string($result);
    }

    public function test_unserialize()
    {
        $this
            ->given(
                $module = new SUT(static::FILE_PATH),
                $serializedModule = serialize($module)
            )
            ->when($result = unserialize($serializedModule, [SUT::class]))
            ->then
                ->object($result)
                    ->isInstanceOf(SUT::class)
                ->integer($result->instantiate()->sum(1, 2))
                    ->isEqualTo(3);
    }
}
