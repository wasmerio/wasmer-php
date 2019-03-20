<?php

declare(strict_types = 1);

namespace Wasm\Cache;

use DirectoryIterator;
use Wasm\Module;

/**
 * An on-disk cache implementation.
 *
 * # Examples
 *
 * ```php,ignore
 * const KEY = 'foobar';
 * const CACHE_DIRECTORY = '/tmp/php.wasm.cache/';
 *
 * $cache = new Wasm\Cache\Filesystem(CACHE_DIRECTORY);
 *
 * // The cache exists. Let's fetch the module.
 * if ($cache->has(KEY)) {
 *     $module = $cache->get(KEY);
 * }
 * // The cache doesn't exist. Let's compile and store the module.
 * else {
 *     $module = new Wasm\Module('my_program.wasm');
 *     $cache->set(KEY, $module);
 * }
 *
 * // Let's continue as usual: Instantiate the module, and invoke functions on it.
 * $instance = $module->instantiate();
 * $instance->sum(1, 2);
 * ```
 */
class Filesystem implements CacheInterface
{
    /**
     * Represents the cache file extension.
     */
    const CACHE_SUFFIX = '.module.wasm.cache';

    /**
     * The cache directory where cache files are located.
     */
    private $cacheDirectory;

    /**
     * Builds a cache in a specific directory.
     *
     * A `Wasm\Cache\Exception` is thrown if the cache directory is not a
     * valid directory, nor readable, nor writable.
     */
    public function __construct(string $cacheDirectory)
    {
        $cacheDirectory = rtrim($cacheDirectory, '/\\');

        if (false === is_dir($cacheDirectory)) {
            throw new Exception("The cache directory `$cacheDirectory` is not a directory.");
        }

        if (false === is_readable($cacheDirectory)) {
            throw new Exception("The cache directory `$cacheDirectory` is not readable.");
        }

        if (false === is_writable($cacheDirectory)) {
            throw new Exception("The cache directory `$cacheDirectory` is not writable.");
        }

        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Gets a module from the cache based on its key if it exists and is
     * valid, the default value otherwise.
     */
    public function get($key, $default = null)
    {
        if (false === $this->has($key)) {
            return $default;
        }

        $serialized_content = file_get_contents($this->getCacheFile($key));

        if (false === $serialized_content) {
            return $default;
        }

        $module = unserialize($serialized_content, [Module::class]);

        if (false === $module) {
            return $default;
        }

        return $module;
    }

    /**
     * Sets a module object into the cached.
     *
     * The TTL is not taken into account yet.
     */
    public function set($key, $value, $ttl = null)
    {
        if (!($value instanceof Module)) {
            throw new InvalidArgumentException('The cache can only store `' . Module::class . '` instances.');
        }

        $filePath = $this->getCacheFile($key);

        file_put_contents($filePath, serialize($value));
    }

    /**
     * Deletes a module from the cache based on its key.
     */
    public function delete($key)
    {
        if (false === $this->has($key)) {
            return;
        }

        unlink($this->getCacheFile($key));
    }

    /**
     * Clears the cache, i.e. remove all cache files.
     */
    public function clear()
    {
        $iterator = new DirectoryIterator($this->cacheDirectory);
        $extensionPattern = '/' . preg_quote(self::CACHE_SUFFIX) . '$/';

        foreach ($iterator as $file) {
            if (false === $file->isFile()) {
                continue;
            }

            if (0 === preg_match($extensionPattern, $file->getBaseName())) {
                continue;
            }

            unlink($file->getPathName());
        }
    }

    /**
     * Not implemented yet.
     */
    public function getMultiple($keys, $default = null)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    /**
     * Not implemented yet.
     */
    public function setMultiple($values, $ttl = null)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    /**
     * Not implemented yet.
     */
    public function deleteMultiple($keys)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    /**
     * Checks whether a cache item exists for a given key.
     */
    public function has($key)
    {
        return file_exists($this->getCacheFile($key));
    }

    /**
     * Gets a cache file name based on a key, whether it exists or not.
     */
    private function getCacheFile(string $key)
    {
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $key . self::CACHE_SUFFIX;
    }
}
