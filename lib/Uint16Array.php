<?php

declare(strict_types = 1);

namespace Wasm;

use WasmUint16Array;

/**
 * Represents a typed array of twos-complement 16-bit unsigned integers in
 * little-endian.
 */
class Uint16Array extends WasmUint16Array implements TypedArray
{
}
