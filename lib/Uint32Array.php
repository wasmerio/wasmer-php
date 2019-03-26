<?php

declare(strict_types = 1);

namespace Wasm;

use WasmUint32Array;

/**
 * Represents a typed array of twos-complement 32-bit unsigned integers in
 * little-endian.
 */
class Uint32Array extends WasmUint32Array implements TypedArray
{
}
