<?php

namespace Wasm\Tests\Units;

use RuntimeException;
use Wasm\Tests\Suite;

class Extension extends Suite
{
    const FILE_PATH = __DIR__ . '/tests.wasm';

    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function test_constants()
    {
        $this
            ->integer(WASM_TYPE_I32)
                ->isEqualTo(0)
            ->integer(WASM_TYPE_I64)
                ->isEqualTo(1)
            ->integer(WASM_TYPE_F32)
                ->isEqualTo(2)
            ->integer(WASM_TYPE_F64)
                ->isEqualTo(3);
    }

    public function test_wasm_read_bytes()
    {
        $this
            ->when($result = wasm_read_bytes(self::FILE_PATH))
            ->then
                ->resource($result)
                    ->isOfType('wasm_bytes');
    }

    public function test_wasm_read_bytes_unknown_file()
    {
        $this
            ->when($result = wasm_read_bytes(__FILE__ . 'foobar'))
            ->then
                ->variable($result)
                   ->isNull();
    }

    public function test_wasm_new_instance()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(self::FILE_PATH))
            ->when($result = wasm_new_instance($wasmBytes))
            ->then
                ->resource($result)
                    ->isOfType('wasm_instance');
    }

    public function test_wasm_new_instance_failed_to_compile()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(__DIR__ . '/empty.wasm'))
            ->when($result = wasm_new_instance($wasmBytes))
            ->then
                ->variable($result)
                    ->isNull();
    }

    /**
     * @dataProvider signatures
     */
    public function test_wasm_get_function_signature(string $functionName, array $signature)
    {
        $this
            ->given(
                $wasmBytes = wasm_read_bytes(self::FILE_PATH),
                $wasmInstance = wasm_new_instance($wasmBytes)
            )
            ->when($result = wasm_get_function_signature($wasmInstance, $functionName))
            ->then
                ->array($result)
                    ->isEqualTo($signature);
    }

    protected function signatures() {
        yield 'arity_0' => [
            'functionName' => 'arity_0',
            'signature' => [
                WASM_TYPE_I32,
            ],
        ];

        yield 'i32_i32' => [
            'functionName' => 'i32_i32',
            'signature' => [
                WASM_TYPE_I32,
                WASM_TYPE_I32,
            ],
        ];

        yield 'i32_i64_f32_f64_f64' => [
            'functionName' => 'i32_i64_f32_f64_f64',
            'signature' => [
                WASM_TYPE_I32,
                WASM_TYPE_I64,
                WASM_TYPE_F32,
                WASM_TYPE_F64,
                WASM_TYPE_F64,
            ],
        ];

        yield 'bool_casted_to_i32' => [
            'functionName' => 'bool_casted_to_i32',
            'signature' => [
                WASM_TYPE_I32,
            ],
        ];
    }

    /**
     * @dataProvider values
     */
    public function test_wasm_value(int $type, $value)
    {
        $this
            ->when($result = wasm_value($type, $value))
            ->then
                ->resource($result)
                    ->isOfType('wasm_value');
    }

    protected function values()
    {
        yield 'i32' => [WASM_TYPE_I32, 1];
        yield 'i64' => [WASM_TYPE_I64, 1];
        yield 'f32' => [WASM_TYPE_F32, 1.];
        yield 'f64' => [WASM_TYPE_F64, 1.];
    }

    public function test_wasm_value_unknown_type()
    {
        $this
            ->when($result = wasm_value(42, 1))
            ->then
                ->variable($result)
                    ->isNull();
    }

    public function test_wasm_invoke_function()
    {
        $this
            ->given(
                $wasmBytes = wasm_read_bytes(self::FILE_PATH),
                $wasmInstance = wasm_new_instance($wasmBytes),
                $wasmArguments = [
                    wasm_value(WASM_TYPE_I32, 1),
                    wasm_value(WASM_TYPE_I32, 2)
                ]
            )
            ->when($result = wasm_invoke_function($wasmInstance, 'sum', $wasmArguments))
            ->then
                ->integer($result)
                    ->isEqualTo(3);
    }

    public function test_wasm_invoke_function_with_wrong_parameters()
    {
        $this
            ->given(
                $wasmBytes = wasm_read_bytes(self::FILE_PATH),
                $wasmInstance = wasm_new_instance($wasmBytes),
                $wasmArguments = [wasm_value(WASM_TYPE_I32, 1)]
            )
            ->when($result = wasm_invoke_function($wasmInstance, 'i64_i64', $wasmArguments))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    /**
     * @dataProvider invokes
     */
    public function test_wasm_invoke_functions(string $functionName, array $inputs, $output)
    {
        $this
            ->given(
                $wasmBytes = wasm_read_bytes(self::FILE_PATH),
                $wasmInstance = wasm_new_instance($wasmBytes),
                $wasmArguments = $inputs
            )
            ->when($result = wasm_invoke_function($wasmInstance, $functionName, $wasmArguments))
            ->then
                ->variable($result)
                    ->isEqualTo($output);
    }

    protected function invokes()
    {
        yield 'arity_0' => [
            'arity_0',
            [],
            42
        ];

        yield 'i32_i32' => [
            'i32_i32',
            [
                wasm_value(WASM_TYPE_I32, 7),
            ],
            7
        ];

        yield 'i64_i64' => [
            'i64_i64',
            [
                wasm_value(WASM_TYPE_I64, 7),
            ],
            7
        ];

        yield 'f32_f32' => [
            'f32_f32',
            [
                wasm_value(WASM_TYPE_F32, 7.),
            ],
            7.
        ];

        yield 'f64_f64' => [
            'f64_f64',
            [
                wasm_value(WASM_TYPE_F64, 7.),
            ],
            7.
        ];

        yield 'i32_i64_f32_f64_f64' => [
            'i32_i64_f32_f64_f64',
            [
                wasm_value(WASM_TYPE_I32, 1),
                wasm_value(WASM_TYPE_I64, 2),
                wasm_value(WASM_TYPE_F32, 3.),
                wasm_value(WASM_TYPE_F64, 4.),
            ],
            10.
        ];

        yield 'bool_casted_to_i32' => [
            'bool_casted_to_i32',
            [],
            1
        ];
    }
}
