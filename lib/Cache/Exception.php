<?php

declare(strict_types = 1);

namespace Wasm\Cache;

use Psr\SimpleCache\CacheException;
use RuntimeException;

/**
 * Represents all errors for the cache implementations.
 */
class Exception extends RuntimeException implements CacheException
{
}
