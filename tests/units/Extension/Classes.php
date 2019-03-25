<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use Exception;
use ReflectionClass;
use ReflectionExtension;
use ReflectionMethod;
use WasmArrayBuffer;
use Wasm\Tests\Suite;

class Classes extends Suite
{
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
                    ->hasSize(1)
                    ->object['WasmArrayBuffer']->isInstanceOf(ReflectionClass::class)

            ->when($_result = $result['WasmArrayBuffer'])
            ->then
                ->array($_result->getConstants())
                    ->isEmpty()

                ->let($methods = $_result->getMethods())

                ->array($methods)
                    ->hasSize(2)

                ->string($methods[0]->getName())
                    ->isEqualTo('__construct')
                ->boolean($methods[0]->isPublic())
                    ->isTrue()
                ->integer($methods[0]->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($methods[0]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[0]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('byte_length')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('int')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

                ->string($methods[1]->getName())
                    ->isEqualTo('getByteLength')
                ->boolean($methods[1]->isPublic())
                    ->isTrue()
                ->integer($methods[1]->getNumberOfParameters())
                    ->isEqualTo(0)
                    ->isEqualTo($methods[1]->getNumberOfRequiredParameters())

                ->let($return_type = $methods[1]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('int')
                ->boolean($return_type->allowsNull())
                    ->isFalse()

                ->boolean($_result->getParentClass())
                    ->isFalse()
                ->array($_result->getProperties())
                    ->isEmpty()
                ->string($_result->getShortName())
                    ->isEqualTo('WasmArrayBuffer')
                ->array($_result->getStaticProperties())
                    ->isEmpty()
                ->boolean($_result->inNamespace())
                    ->isFalse()
                ->boolean($_result->isAbstract())
                    ->isFalse()
                ->boolean($_result->isAnonymous())
                    ->isFalse()
                ->boolean($_result->isCloneable())
                    ->isFalse()
                ->boolean($_result->isFinal())
                    ->isTrue()
                ->boolean($_result->isInstantiable())
                    ->isTrue()
                ->boolean($_result->isInternal())
                    ->isTrue();
    }

    public function test_wasm_array_buffer_constructor()
    {
        $this
            ->when($result = new WasmArrayBuffer(42))
            ->then
                ->object($result)
                    ->isInstanceOf(WasmArrayBuffer::class);
    }

    public function test_wasm_array_buffer_of_length_0()
    {
        $this
            ->exception(
                function() {
                    new WasmArrayBuffer(0);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage('Buffer length must be positive.')
                ->hasCode(0);
    }

    public function test_wasm_array_buffer_with_a_negative_length()
    {
        $this
            ->exception(
                function() {
                    new WasmArrayBuffer(-1);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage('Buffer length must be positive.')
                ->hasCode(0);
    }

    public function test_wasm_array_buffer_get_byte_length()
    {
        $this
            ->given(
                $byteLength = 42,
                $wasmArrayBuffer = new WasmArrayBuffer($byteLength)
            )
            ->when($result = $wasmArrayBuffer->getByteLength())
            ->then
                ->integer($result)
                    ->isEqualTo($byteLength);
    }
}
