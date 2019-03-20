<?php

declare(strict_types = 1);

namespace Wasm\Cache;

use Psr\SimpleCache;

/**
 * A cache interface that forces to implement
 * [PSR-16](https://www.php-fig.org/psr/psr-16/).
 */
interface CacheInterface extends SimpleCache\CacheInterface
{
}
