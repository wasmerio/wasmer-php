<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units;

use Wasm as LUT;
use WasmArrayBuffer;
use WasmInt16Array;
use WasmInt32Array;
use WasmInt8Array;
use WasmUint16Array;
use WasmUint32Array;
use WasmUint8Array;
use Wasm\Tests\Suite;

class TypedArray extends Suite
{
    const FILE_PATH = __DIR__ . '/tests.wasm';

    /**
     * @dataProvider typed_arrays
     */
    public function test_typed_arrays(string $typedArrayName, string $wasmTypedArrayName)
    {
        $this
            ->given($wasmArrayBuffer = new WasmArrayBuffer(8))
            ->when($result = new $typedArrayName($wasmArrayBuffer))
            ->then
                ->object($result)
                    ->isInstanceOf($wasmTypedArrayName)
                    ->isInstanceof(LUT\TypedArray::class);
    }

    /**
     * @dataProvider typed_arrays
     */
    public function test_length(string $typedArrayName)
    {
        $this
            ->given(
                $instance = new LUT\Instance(static::FILE_PATH),
                $memoryBuffer = $instance->getMemoryBuffer(),
                $memoryBufferByteLength = $memoryBuffer->getByteLength()
            )
            ->when($typedArray = new $typedArrayName($memoryBuffer))
            ->then
                ->integer($typedArray->getLength())
                    ->isEqualTo($memoryBuffer->getByteLength() / $typedArrayName::BYTES_PER_ELEMENT);
    }

    protected function typed_arrays()
    {
        yield [LUT\Int8Array::class,   WasmInt8Array::class];
        yield [LUT\Int16Array::class,  WasmInt16Array::class];
        yield [LUT\Int32Array::class,  WasmInt32Array::class];
        yield [LUT\Uint8Array::class,  WasmUint8Array::class];
        yield [LUT\Uint16Array::class, WasmUint16Array::class];
        yield [LUT\Uint32Array::class, WasmUint32Array::class];
    }
}
