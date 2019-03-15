<?php

declare(strict_types = 1);

namespace Wasm\Cache;

use Psr\SimpleCache\CacheException;
use RuntimeException;

class Exception extends RuntimeException implements CacheException
{
}
