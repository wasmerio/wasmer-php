<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units;

use RuntimeException;
use Wasm\Instance as SUT;
use Wasm\InvocationException;
use Wasm\Tests\Suite;

class Instance extends Suite
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
                ->hasMessage("An error happened while compiling or instantiating the module `$filePath`:\n    ");
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
                ->hasMessage(
                    "An error happened while compiling or instantiating the module `$filePath`:\n" .
                    "    error instantiating"
                );
    }

    public function test_constructor_invalid_instantiation()
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
                    "An error happened while compiling or instantiating the module `$filePath`:\n" .
                    "    error instantiating"
                );
    }

    public function test_basic_sum()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->sum(1, 2))
            ->then
                ->integer($result)
                    ->isEqualTo(3);
    }

    public function test_call_undefined_function()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->exception(
                function () use ($wasmInstance) {
                    $wasmInstance->ƒ();
                }
            )
                ->isInstanceOf(InvocationException::class)
                ->hasMessage('Function `ƒ` does not exist.');
    }

    public function test_call_missing_arguments()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->exception(
                function () use ($wasmInstance) {
                    $wasmInstance->sum(1);
                }
            )
                ->isInstanceOf(InvocationException::class)
                ->hasMessage(
                    'Missing 1 argument(s) when calling `sum`: ' .
                    'Expect 2 arguments, given 1.'
                );
    }

    public function test_call_extra_arguments()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->exception(
                function () use ($wasmInstance) {
                    $wasmInstance->sum(1, 2, 3);
                }
            )
                ->isInstanceOf(InvocationException::class)
                ->hasMessage(
                    'Given 1 extra argument(s) when calling `sum`: ' .
                    'Expect 2 arguments, given 3.'
                );
    }

    public function test_call_arity_0()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->arity_0())
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    public function test_call_i32_i32()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->i32_i32(7))
            ->then
                ->integer($result)
                    ->isEqualTo(7);
    }

    public function test_call_i64_i64()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->i64_i64(7))
            ->then
                ->integer($result)
                    ->isEqualTo(7);
    }

    public function test_call_f32_f32()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->f32_f32(7.42))
            ->then
                ->float($result)
                    ->isNearlyEqualTo(7.42, 64);
    }

    public function test_call_f64_f64()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->f64_f64(7.42))
            ->then
                ->float($result)
                    ->isNearlyEqualTo(7.42, 64);
    }

    public function test_call_i32_i64_f32_f64_f64()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when(
                $result = $wasmInstance->i32_i64_f32_f64_f64(
                    1,
                    2,
                    3.4,
                    5.6
                )
            )
            ->then
                ->float($result)
                    ->isNearlyEqualTo(1 + 2 + 3.4 + 5.6, 64);
    }

    public function test_call_bool_casted_to_i32()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->bool_casted_to_i32())
            ->then
                ->integer($result)
                    ->isEqualTo(1);
    }
}
