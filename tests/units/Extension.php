<?php

namespace Wasm\Tests\Units;

use ReflectionExtension;
use ReflectionFunction;
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

    public function test_reflection_classes()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getClasses())
            ->then
                ->array($result)
                    ->isEmpty()

            ->when($result = $reflection->getClassNames())
            ->then
                ->array($result)
                    ->isEmpty();
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

    public function test_reflection_dependencies()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getDependencies())
            ->then
                ->array($result)
                    ->isEmpty();
    }

    public function test_reflection_functions()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getFunctions())
            ->then
                ->array($result)
                    ->hasSize(8)
                    ->object['wasm_read_bytes']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_validate']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_compile']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_new_instance']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_get_function_signature']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_value']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_invoke_function']->isInstanceOf(ReflectionFunction::class)
                    ->object['wasm_get_last_error']->isInstanceOf(ReflectionFunction::class)

            ->when($_result = $result['wasm_read_bytes'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('file_path')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('string')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_validate'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_bytes')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('resource')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_compile'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_bytes')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('resource')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_new_instance'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_bytes')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('resource')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_get_function_signature'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(2)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_instance')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('resource')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

                ->string($parameters[1]->getName())
                    ->isEqualTo('function_name')
                ->string($parameters[1]->getType() . '')
                    ->isEqualTo('string')
                ->boolean($parameters[1]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_value'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(2)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('type')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('int')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

                ->string($parameters[1]->getName())
                    ->isEqualTo('value')
                ->boolean($parameters[1]->hasType())
                    ->isFalse()

            ->when($_result = $result['wasm_invoke_function'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(3)
                    ->isEqualTo($_result->getNumberOfRequiredParameters())

                ->let($parameters = $_result->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_instance')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('resource')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

                ->string($parameters[1]->getName())
                    ->isEqualTo('function_name')
                ->string($parameters[1]->getType() . '')
                    ->isEqualTo('string')
                ->boolean($parameters[1]->getType()->allowsNull())
                    ->isFalse()

                ->string($parameters[2]->getName())
                    ->isEqualTo('inputs')
                ->string($parameters[2]->getType() . '')
                    ->isEqualTo('array')
                ->boolean($parameters[2]->getType()->allowsNull())
                    ->isFalse()

            ->when($_result = $result['wasm_get_last_error'])
            ->then
                ->integer($_result->getNumberOfParameters())
                    ->isEqualTo(0)
                    ->isEqualTo($_result->getNumberOfRequiredParameters());
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

    public function test_reflection_name()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getName())
            ->then
                ->string($result)
                    ->isEqualTo('wasm');
    }

    public function test_reflection_version()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getVersion())
            ->then
                ->string($result)
                    ->isEqualTo('0.2.0');
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

    public function test_wasm_validate()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(self::FILE_PATH))
            ->when($result = wasm_validate($wasmBytes))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function test_wasm_validate_nop()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(__DIR__ . '/invalid.wasm'))
            ->when($result = wasm_validate($wasmBytes))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function test_wasm_compile()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(self::FILE_PATH))
            ->when($result = wasm_compile($wasmBytes))
            ->then
                ->resource($result)
                    ->isOfType('wasm_module');
    }

    public function test_wasm_compile_invalid_bytes()
    {
        $this
            ->given($wasmBytes = wasm_read_bytes(__DIR__ . '/invalid.wasm'))
            ->when($result = wasm_compile($wasmBytes))
            ->then
                ->variable($result)
                    ->isNull()
                ->string(wasm_get_last_error())
                    ->isEqualTo('Validation error "Invalid type"');
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

    public function test_wasm_get_last_error()
    {
        $this
            ->given(
                $wasmBytes = wasm_read_bytes(self::FILE_PATH),
                $wasmInstance = wasm_new_instance($wasmBytes),
                $wasmArguments = []
            )
            ->when($result = wasm_invoke_function($wasmInstance, 'sum', $wasmArguments))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = wasm_get_last_error())
                ->string($result)
                    ->isEqualTo('Call error: Parameters of type [] did not match signature [I32, I32] -> [I32]');
    }

    public function test_wasm_get_last_error_without_any_error()
    {
        $this
            ->when($result = wasm_get_last_error())
                ->variable($result)
                    ->isNull();
    }
}
