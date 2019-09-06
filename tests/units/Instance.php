<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units;

use Exception;
use RuntimeException;
use Wasm as LUT;
use WasmArrayBuffer;
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
                    "    compile error: Validation error \"Invalid type\""
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
                    "    compile error: Validation error \"Unexpected EOF\""
                );
    }

    public function test_get_memory_buffer()
    {
        $this
            ->given(
                $wasmInstance = new SUT(self::FILE_PATH),
                $stringPointer = $wasmInstance->string()
            )
            ->when($result = $wasmInstance->getMemoryBuffer())
            ->then
                ->object($result)
                    ->isInstanceOf(WasmArrayBuffer::class)

            ->let($string = '')
            ->when(
                function () use ($result, $stringPointer, &$string) {
                    $view = new LUT\Uint8Array($result, $stringPointer);

                    $this
                        ->integer($view->getOffset())
                            ->isEqualTo($stringPointer);

                    $nth = 0;

                    while (0 !== $view[$nth]) {
                        $string .= chr($view[$nth]);
                        ++$nth;
                    }
                }
            )
            ->then
                ->string($string)
                    ->isEqualTo('Hello, World!');
    }

    public function test_grow_memory_buffer()
    {
        $this
            ->given(
                $wasmInstance = new SUT(self::FILE_PATH),
                $memory = $wasmInstance->getMemoryBuffer(),
                $oldMemoryLength = $memory->getByteLength()
            )
            ->when($result = $memory->grow(1))
            ->then
                ->variable($result)
                    ->isNull()
                ->integer($oldMemoryLength)
                    ->isEqualTo(1114112)

                ->let($memoryLength = $memory->getByteLength())

                ->integer($memoryLength)
                    ->isEqualTo(1179648)

                ->integer($memoryLength - $oldMemoryLength)
                    ->isEqualTo(65536);
    }

    public function test_grow_memory_buffer_too_much()
    {
        $this
            ->given(
                $wasmInstance = new SUT(self::FILE_PATH),
                $memory = $wasmInstance->getMemoryBuffer()
            )
            ->exception(function () use ($memory) {
                $memory->grow(100000);
            })
                ->isInstanceOf(\Exception::class)
                ->hasMessage('Failed to grow the memory.');
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
                ->hasMessage('Got an error when invoking `ƒ`: The instance has no exported function named `ƒ`.');
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
                    'Got an error when invoking `sum`: ' .
                    'Missing 1 argument(s) when calling the `sum` exported function; ' .
                    'Expect 2 argument(s), given 1.'
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
                    'Got an error when invoking `sum`: ' .
                    'Given 1 extra argument(s) when calling the `sum` exported function; ' .
                    'Expect 2 argument(s), given 3.'
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

    public function test_call_string()
    {
        $this
            ->given($wasmInstance = new SUT(self::FILE_PATH))
            ->when($result = $wasmInstance->string())
            ->then
                ->integer($result)
                    ->isEqualTo(1048576);
    }

    private function get_imported_functions(): array
    {
        return [
            'env' => [
                '_sum' => function (int $x, int $y): int {
                    return $x + $y;
                },
                '_arity_0' => function (): int {
                    return 42;
                },
                '_i32_i32' => function (int $x): int {
                    return $x;
                },
                '_void' => function (): void {}
            ]
        ];
    }

    public function test_imported_functions()
    {
        $this
            ->given(
                $importedFunctions = $this->get_imported_functions(),
                $wasmInstance = new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions)
            )
            ->when($result = $wasmInstance->sum(1, 2))
                ->integer($result)
                    ->isEqualTo(3)

            ->when($result = $wasmInstance->arity_0())
                ->integer($result)
                    ->isEqualTo(42)

            ->when($result = $wasmInstance->i32_i32(7))
                ->integer($result)
                    ->isEqualTo(7)

            ->when($result = $wasmInstance->void())
                ->variable($result)
                    ->isNull();
    }

    public function test_malformed_imported_functions_missing_imported_functions() {
        $this
            ->exception(function () {
                new SUT(__DIR__ . '/imported_functions_tests.wasm', ['env' => 'foo']);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('Imported functions must be of the form `[\'module_name\' => [\'imported_function_name\' => callable, ...], ...]`, for key `env`.');
    }

    public function test_malformed_imported_functions_optional_argument() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, int $y, int $z = null): int {
                    return $x + $y;
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('Imported function `env.sum` (implemented by `Closure::__invoke`) has either an optional argument or is variadic; both are unsupported.');
    }

    public function test_malformed_imported_functions_pass_by_reference_argument() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, int &$y): int {
                    return $x + $y;
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The argument `$y` of the imported function `env.sum` (implemented by `Closure::__invoke`) is a reference; this is not supported.');
    }

    public function test_malformed_imported_functions_invalid_argument_type() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, string $y): int {
                    return $x + $y;
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The argument `$y` of the imported function `env.sum` (implemented by `Closure::__invoke`) must be an integer; given `string`.');
    }

    public function test_malformed_imported_functions_missing_argument_type() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, $y): int {
                    return $x + $y;
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The argument `$y` of the imported function `env.sum` (implemented by `Closure::__invoke`) must be an integer; given `unknown`.');
    }

    public function test_malformed_imported_functions_missing_return_type() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, int $y) {
                    return $x + $y;
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The return type of the imported function `env.sum` (implemented by `Closure::__invoke`) must be an integer; given none.');
    }

    public function test_malformed_imported_functions_invalid_return_type() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = function(int $x, int $y): string {
                    return (string) ($x + $y);
                };

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The return type of the imported function `env.sum` (implemented by `Closure::__invoke`) must be an integer; given `string`.');
    }

    public function test_malformed_imported_functions_not_a_callable() {
        $this
            ->exception(function () {
                $importedFunctions = $this->get_imported_functions();
                $importedFunctions['env']['sum'] = 42;

                new SUT(__DIR__ . '/imported_functions_tests.wasm', $importedFunctions);
            })
                ->isInstanceOf(Exception::class)
                ->hasMessage('The imported function `env.sum` must be a valid callable.');
    }
}
