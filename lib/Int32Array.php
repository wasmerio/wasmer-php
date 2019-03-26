<?php

declare(strict_types = 1);

namespace Wasm;

use WasmInt32Array;

/**
 * Represents a typed array of twos-complement 32-bit signed integers in
 * little-endian.
 */
class Int32Array extends WasmInt32Array implements TypedArray
{
}
