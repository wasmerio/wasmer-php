<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Extension;

use ArrayAccess;
use Exception;
use ReflectionClass;
use ReflectionExtension;
use ReflectionMethod;
use StdClass;
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
        return StdClass::class;
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
                    ->hasSize(3)

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

                ->string($methods[2]->getName())
                    ->isEqualTo('grow')
                ->boolean($methods[2]->isPublic())
                    ->isTrue()
                ->integer($methods[2]->getNumberOfParameters())
                    ->isEqualTo(1)
                    ->isEqualTo($methods[2]->getNumberOfRequiredParameters())

                ->let($parameters = $methods[2]->getParameters())

                ->string($parameters[0]->getName())
                    ->isEqualTo('number_of_pages')
                ->string($parameters[0]->getType() . '')
                    ->isEqualTo('int')
                ->boolean($parameters[0]->getType()->allowsNull())
                    ->isFalse()

                ->let($return_type = $methods[2]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('void')
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
            ->given(
                $reflection = new ReflectionExtension('wasm'),
                $bytesPerElement = [
                    WasmInt8Array::class => 1,
                    WasmUint8Array::class => 1,
                    WasmInt16Array::class => 2,
                    WasmUint16Array::class => 2,
                    WasmInt32Array::class => 4,
                    WasmUint32Array::class => 4,
                ]
            )
            ->when($result = $reflection->getClasses()[$wasmTypedArrayClassName])
            ->then
                ->array($result->getConstants())
                    ->hasSize(1)
                    ->isEqualTo([
                        'BYTES_PER_ELEMENT' => $bytesPerElement[$wasmTypedArrayClassName] ?? -1,
                    ])

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
                    ->isEqualTo(PHP_VERSION_ID < 70300 ? 'unknown' : 'number')
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

                ->let($return_type = $methods[4]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('void')
                ->boolean($return_type->allowsNull())
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

                ->let($return_type = $methods[6]->getReturnType())

                ->string($return_type . '')
                    ->isEqualTo('void')
                ->boolean($return_type->allowsNull())
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
                    ->isFalse()
                ->boolean($result->isInstantiable())
                    ->isTrue()
                ->boolean($result->isInternal())
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
                ->hasMessage('Buffer length must be positive; given 0.')
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
                ->hasMessage('Buffer length must be positive; given -1.')
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

    public function test_wasm_array_buffer_grow()
    {
        $this
            ->given(
                $byteLength = 1114112,
                $wasmArrayBuffer = new WasmArrayBuffer($byteLength)
            )
            ->when($result = $wasmArrayBuffer->grow(1))
            ->then
                ->variable($result)
                    ->isNull()
                // Does nothing because it's not Wasm memory, just an allocated buffer.
                ->integer($wasmArrayBuffer->getByteLength())
                    ->isEqualTo($byteLength);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $bufferLength = 256,
                $wasmArrayBuffer = new WasmArrayBuffer($bufferLength)
            )
            ->when($result = new $wasmTypedArrayClassName($wasmArrayBuffer))
            ->then
                ->integer($result->getOffset())
                    ->isZero()
                ->integer($result->getLength())
                    ->isEqualTo($bufferLength / $result::BYTES_PER_ELEMENT)
                ->when(
                    function () use ($result) {
                        for ($i = 0; $i < $result->getLength(); ++$i) {
                            $this
                                ->integer($result[$i])
                                    ->isEqualTo(0);
                        }
                    }
                );
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_an_offset(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $bufferLength = 256,
                $offset = 3,
                $wasmArrayBuffer = new WasmArrayBuffer($bufferLength)
            )
            ->when($result = new $wasmTypedArrayClassName($wasmArrayBuffer, $offset))
            ->then
                ->integer($result->getOffset())
                    ->isEqualTo($offset)
                ->integer($result->getLength())
                    ->isEqualTo((int) (($bufferLength - $offset) / $result::BYTES_PER_ELEMENT));
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_an_offset_and_a_length(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $bufferLength = 256,
                $offset = 3,
                $length = 12,
                $wasmArrayBuffer = new WasmArrayBuffer($bufferLength)
            )
            ->when($result = new $wasmTypedArrayClassName($wasmArrayBuffer, $offset, $length))
            ->then
                ->integer($result->getOffset())
                    ->isEqualTo($offset)
                ->integer($result->getLength())
                    ->isEqualTo($length);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_a_negative_offset(string $wasmTypedArrayClassName)
    {
        $this
            ->given($wasmArrayBuffer = new WasmArrayBuffer(256))
            ->exception(
                function () use ($wasmArrayBuffer, $wasmTypedArrayClassName) {
                    new $wasmTypedArrayClassName($wasmArrayBuffer, -1);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage('Offset must be non-negative; given -1.');
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_a_too_large_offset(string $wasmTypedArrayClassName)
    {
        $this
            ->given($wasmArrayBuffer = new WasmArrayBuffer(256))
            ->exception(
                function () use ($wasmArrayBuffer, $wasmTypedArrayClassName) {
                    new $wasmTypedArrayClassName($wasmArrayBuffer, 257);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage('Offset must be smaller than the array buffer length; given 257, buffer length is 256.');
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_a_negative_length(string $wasmTypedArrayClassName)
    {
        $this
            ->given($wasmArrayBuffer = new WasmArrayBuffer(256))
            ->exception(
                function () use ($wasmArrayBuffer, $wasmTypedArrayClassName) {
                    new $wasmTypedArrayClassName($wasmArrayBuffer, 2, -1);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage('Length must be non-negative; given -1.');
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_constructor_with_a_too_large_length(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer(256),
                $maximumLength = 256 / $wasmTypedArrayClassName::BYTES_PER_ELEMENT
            )
            ->exception(
                function () use ($wasmArrayBuffer, $wasmTypedArrayClassName) {
                    new $wasmTypedArrayClassName($wasmArrayBuffer, 0, 257);
                }
            )
                ->isInstanceOf(Exception::class)
                ->hasMessage("Length must not be greater than the buffer length; given 257, maximum length is $maximumLength.");
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_set_get(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer),
                $wasmTypedArray[0] = 42
            )
            ->when($result = $wasmTypedArray[0])
            ->then
                ->integer($result)
                    ->isEqualTo(42);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_get_out_of_range(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer)
            )
            ->exception(
                function () use ($wasmTypedArray) {
                    return $wasmTypedArray[1];
                }
            )
                ->isInstanceOf(Exception::class);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_set_out_of_range(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer)
            )
            ->exception(
                function () use ($wasmTypedArray) {
                    $wasmTypedArray[1] = 42;
                }
            )
                ->isInstanceOf(Exception::class);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_exists(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer)
            )
            ->when($result = isset($wasmTypedArray[0]))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_does_not_exist(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer)
            )
            ->when($result = isset($wasmTypedArray[1]))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = isset($wasmTypedArray[-1]))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_unset(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer),
                $wasmTypedArray[0] = 42
            )
            ->when($result = $wasmTypedArray[0])
            ->then
                ->integer($result)
                    ->isEqualTo(42)

            ->when(
                function () use ($wasmTypedArray) {
                    unset($wasmTypedArray[0]);
                }
            )
            ->when($result = $wasmTypedArray[0])
            ->then
                ->integer($result)
                    ->isEqualTo(0);
    }

    /**
     * @dataProvider wasm_typed_arrays
     */
    public function test_wasm_typed_array_unset_out_of_range(string $wasmTypedArrayClassName)
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer($wasmTypedArrayClassName::BYTES_PER_ELEMENT),
                $wasmTypedArray = new $wasmTypedArrayClassName($wasmArrayBuffer)
            )
            ->exception(
                function () use ($wasmTypedArray) {
                    unset($wasmTypedArray[1]);
                }
            )
                ->isInstanceOf(Exception::class);
    }

    public function test_wasm_typed_array_share_the_same_buffer()
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer(256),
                $int8 = new WasmInt8Array($wasmArrayBuffer),
                $int16 = new WasmInt16Array($wasmArrayBuffer),
                $int32 = new WasmInt32Array($wasmArrayBuffer)
            )
            ->when(
                $int8[0] = 0b00000001,
                $int8[1] = 0b00000100,
                $int8[2] = 0b00010000,
                $int8[3] = 0b01000000
            )
            ->then
                ->integer($int8[0])
                    ->isEqualTo(0b00000001)
                ->integer($int8[1])
                    ->isEqualTo(0b00000100)
                ->integer($int8[2])
                    ->isEqualTo(0b00010000)
                ->integer($int8[3])
                    ->isEqualTo(0b01000000)
                ->integer($int16[0])
                    ->isEqualTo(0b0000010000000001)
                ->integer($int16[1])
                    ->isEqualTo(0b0100000000010000)
                ->integer($int32[0])
                    ->isEqualTo(0b01000000000100000000010000000001);
    }

    public function test_wasm_typed_array_is_little_endian()
    {
        $this
            ->given(
                $wasmArrayBuffer = new WasmArrayBuffer(256),
                $uint8 = new WasmUint8Array($wasmArrayBuffer),
                $uint16 = new WasmUint16Array($wasmArrayBuffer),
                $uint32 = new WasmUint32Array($wasmArrayBuffer)
            )
            ->when($uint32[0] = 0b00000000000000000000000000000001)
            ->then
                ->integer($uint8[0])
                   ->isEqualTo(0b00000001)
                ->integer($uint8[1])
                   ->isEqualTo(0b00000000)
                ->integer($uint8[2])
                   ->isEqualTo(0b00000000)
                ->integer($uint8[3])
                   ->isEqualTo(0b00000000)
                ->integer($uint16[0])
                   ->isEqualTo(0b000000000000000001)
                ->integer($uint16[1])
                   ->isEqualTo(0b000000000000000000)
                ->integer($uint32[0])
                    ->isEqualTo(0b00000000000000000000000000000001);
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
}
