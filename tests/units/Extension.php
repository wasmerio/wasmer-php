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

    public function test_wasm_new_instance()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(self::FILE_PATH))
            ->when($result = wasm_new_instance($wasmBytes))
            ->then
                ->resource($result)
                    ->isOfType('wasm_instance');
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
}
