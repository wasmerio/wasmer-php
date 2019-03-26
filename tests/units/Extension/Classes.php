<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ArrayAccess;
use Exception;
use ReflectionClass;
use ReflectionExtension;
use ReflectionMethod;
use WasmArrayBuffer;
use WasmInt16Array;
use WasmInt32Array;
use WasmInt8Array;
use WasmUint16Array;
use WasmUint32Array;
use WasmUint8Array;
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
                    ->hasSize(7)
                    ->object['WasmArrayBuffer']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmInt8Array']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmUint8Array']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmInt16Array']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmUint16Array']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmInt32Array']->isInstanceOf(ReflectionClass::class)
                    ->object['WasmUint32Array']->isInstanceOf(ReflectionClass::class);
    }

    public function test_reflection_wasm_array_buffer()
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getClasses()['WasmArrayBuffer'])
            ->then
                ->array($result->getConstants())
                    ->isEmpty()

                ->let($methods = $result->getMethods())

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

                ->boolean($result->getParentClass())
                    ->isFalse()
                ->array($result->getProperties())
                    ->isEmpty()
                ->string($result->getShortName())
                    ->isEqualTo('WasmArrayBuffer')
                ->array($result->getStaticProperties())
                    ->isEmpty()
                ->boolean($result->inNamespace())
                    ->isFalse()
                ->boolean($result->isAbstract())
                    ->isFalse()
                ->boolean($result->isAnonymous())
                    ->isFalse()
                ->boolean($result->isCloneable())
                    ->isFalse()
                ->boolean($result->isFinal())
                    ->isTrue()
                ->boolean($result->isInstantiable())
                    ->isTrue()
                ->boolean($result->isInternal())
                    ->isTrue();
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_reflection_wasm_typed_array(string $wasmTypedArrayClassName)
    {
        $this
            ->given($reflection = new ReflectionExtension('wasm'))
            ->when($result = $reflection->getClasses()[$wasmTypedArrayClassName])
            ->then
                ->array($result->getConstants())
                    ->isEmpty()

                ->let($methods = $result->getMethods())

                ->array($methods)
                    ->hasSize(7)

                ->string($methods[0]->getName())
                    ->isEqualTo('__construct')
                ->boolean($methods[0]->isPublic())
                    ->isTrue()
                ->integer($methods[0]->getNumberOfParameters())
                    ->isEqualTo(3)
                ->integer($methods[0]->getNumberOfRequiredParameters())
                    ->isEqualTo(1)

                ->let($parameters = $methods[0]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('wasm_array_buffer')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo(WasmArrayBuffer::class)
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()
                ->boolean($parameters[0]->isOptional())
                    ->isFalse()

                ->string($parameters[1]->getName())
                    ->isEqualTo('offset')
                ->string($parameters[1]->getType() . '')
                    ->isEqualTo('int')
                ->boolean($parameters[1]->getType()->allowsNull())
                    ->isFalse()
                ->boolean($parameters[1]->isOptional())
                    ->isTrue()

                ->string($parameters[2]->getName())
                    ->isEqualTo('length')
                ->string($parameters[1]->getType() . '')
                    ->isEqualTo('int')
                ->boolean($parameters[2]->getType()->allowsNull())
                    ->isFalse()
                ->boolean($parameters[2]->isOptional())
                    ->isTrue()

                ->string($methods[1]->getName())
                    ->isEqualTo('getOffset')
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

                ->string($methods[2]->getName())
                    ->isEqualTo('getLength')
                ->boolean($methods[2]->isPublic())
                    ->isTrue()
                ->integer($methods[2]->getNumberOfParameters())
                    ->isEqualTo(0)
                    ->isEqualTo($methods[2]->getNumberOfRequiredParameters())

                ->let($return_type = $methods[2]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('int')
                ->boolean($return_type->allowsNull())
                    ->isFalse()

                ->string($methods[3]->getName())
                    ->isEqualTo('offsetGet')
                ->boolean($methods[3]->isPublic())
                    ->isTrue()
                ->integer($methods[3]->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($methods[3]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[3]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('offset')
                ->boolean($parameters[0]->hasType())
                    ->isFalse()

                ->let($return_type = $methods[3]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('number')
                ->boolean($return_type->allowsNull())
                    ->isFalse()

                ->string($methods[4]->getName())
                    ->isEqualTo('offsetSet')
                ->boolean($methods[4]->isPublic())
                    ->isTrue()
                ->integer($methods[4]->getNumberOfParameters())
                    ->isEqualTo(2)
                    ->isEqualTo($methods[4]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[4]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('offset')
                ->boolean($parameters[0]->hasType())
                    ->isFalse()

                ->string($parameters[1]->getName())
                    ->isEqualTo('value')
                ->boolean($parameters[1]->hasType())
                    ->isFalse()

                ->boolean($methods[4]->hasReturnType())
                    ->isFalse()

                ->string($methods[5]->getName())
                    ->isEqualTo('offsetExists')
                ->boolean($methods[5]->isPublic())
                    ->isTrue()
                ->integer($methods[5]->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($methods[5]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[5]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('offset')
                ->boolean($parameters[0]->hasType())
                    ->isFalse()

                ->let($return_type = $methods[5]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('bool')
                ->boolean($return_type->allowsNull())
                    ->isFalse()

                ->string($methods[6]->getName())
                    ->isEqualTo('offsetUnset')
                ->boolean($methods[6]->isPublic())
                    ->isTrue()
                ->integer($methods[6]->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($methods[6]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[6]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('offset')
                ->boolean($parameters[0]->hasType())
                    ->isFalse()

                ->boolean($methods[6]->hasReturnType())
                    ->isFalse()

                ->boolean($result->implementsInterface(ArrayAccess::class))
                    ->isTrue()
                ->boolean($result->getParentClass())
                    ->isFalse()
                ->array($result->getProperties())
                    ->isEmpty()
                ->string($result->getShortName())
                    ->isEqualTo($wasmTypedArrayClassName)
                ->array($result->getStaticProperties())
                    ->isEmpty()
                ->boolean($result->inNamespace())
                    ->isFalse()
                ->boolean($result->isAbstract())
                    ->isFalse()
                ->boolean($result->isAnonymous())
                    ->isFalse()
                ->boolean($result->isCloneable())
                    ->isFalse()
                ->boolean($result->isFinal())
                    ->isTrue()
                ->boolean($result->isInstantiable())
                    ->isTrue()
                ->boolean($result->isInternal())
                    ->isTrue();
    }

    protected function wasm_typed_arrays()
    {
        yield [WasmInt8Array::class];
        yield [WasmUint8Array::class];
        yield [WasmInt16Array::class];
        yield [WasmUint16Array::class];
        yield [WasmInt32Array::class];
        yield [WasmUint32Array::class];
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
