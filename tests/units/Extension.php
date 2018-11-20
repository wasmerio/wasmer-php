<?php

namespace WASM\Tests\Units;

use RuntimeException;
use WASM\Tests\Suite;

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
            ->integer(WASM_SIGNATURE_TYPE_I32)
                ->isEqualTo(0)
            ->integer(WASM_SIGNATURE_TYPE_I64)
                ->isEqualTo(1)
            ->integer(WASM_SIGNATURE_TYPE_F32)
                ->isEqualTo(2)
            ->integer(WASM_SIGNATURE_TYPE_F64)
                ->isEqualTo(3)
            ->integer(WASM_SIGNATURE_TYPE_VOID)
                ->isEqualTo(4);
    }

    public function test_wasm_read_binary()
    {
        $this
            ->when($result = wasm_read_binary(self::FILE_PATH))
            ->then
                ->resource($result)
                    ->isOfType('wasm_binary');
    }

    public function test_wasm_new_instance()
    {
        $this
            ->given($wasmBinary = wasm_read_binary(self::FILE_PATH))
            ->when($result = wasm_new_instance(self::FILE_PATH, $wasmBinary))
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
                $wasmBinary = wasm_read_binary(self::FILE_PATH),
                $wasmInstance = wasm_new_instance(self::FILE_PATH, $wasmBinary)
            )
            ->when($result = wasm_get_function_signature($wasmInstance, $functionName))
            ->then
                ->array($result)
                    ->isEqualTo($signature);
    }

    protected function signatures() {
        yield [
            'functionName' => 'arity_0',
            'signature' => [
                WASM_SIGNATURE_TYPE_I32,
            ]
        ];

        yield [
            'functionName' => 'void',
            'signature' => [
                WASM_SIGNATURE_TYPE_VOID,
            ]
        ];

        yield [
            'functionName' => 'i32_i64_f32_f64_f64',
            'signature' => [
                WASM_SIGNATURE_TYPE_I32,
                WASM_SIGNATURE_TYPE_I64,
                WASM_SIGNATURE_TYPE_F32,
                WASM_SIGNATURE_TYPE_F64,
                WASM_SIGNATURE_TYPE_F64,
            ]
        ];

        yield [
            'functionName' => 'bool_casted_to_i32',
            'signature' => [
                WASM_SIGNATURE_TYPE_I32,
            ]
        ];
    }

    public function test_wasm_invoke_function()
    {
        $this
            ->given(
                $wasmBinary = wasm_read_binary(self::FILE_PATH),
                $wasmInstance = wasm_new_instance(self::FILE_PATH, $wasmBinary),
                $wasmArguments = wasm_invoke_arguments_builder(),
                wasm_invoke_arguments_builder_add_i32($wasmArguments, 1),
                wasm_invoke_arguments_builder_add_i32($wasmArguments, 2)
            )
            ->when($result = wasm_invoke_function($wasmInstance, 'sum', $wasmArguments))
            ->then
                ->integer($result)
                    ->isEqualTo(3);
    }

    public function test_wasm_arguments_builder()
    {
        $this
            ->when($result = wasm_invoke_arguments_builder())
            ->then
                ->resource($result)
                    ->isOfType('wasm_invoke_arguments_builder');
    }
}
