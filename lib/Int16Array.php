<?php

declare(strict_types = 1);

namespace Wasm;

use WasmInt16Array;

/**
 * Represents a typed array of twos-complement 16-bit signed integers in
 * little-endian.
 */
class Int16Array extends WasmInt16Array implements TypedArray
{
}
