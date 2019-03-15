<?php

declare(strict_types = 1);

namespace Wasm\Cache;

use DirectoryIterator;
use Psr\SimpleCache\CacheInterface;

/**
 * A simple cache implementation, used to store module serializations.
 */
class Cache implements CacheInterface
{
    const CACHE_SUFFIX = '.module.wasm.cache';
    private $cacheDirectory;

    public function __construct(string $cacheDirectory)
    {
        $cacheDirectory = rtrim($cacheDirectory, '/\\');

        if (false === is_dir($cacheDirectory)) {
            throw new Exception("The cache directory `$cacheDirectory` is not a directory.");
        }

        $this->cacheDirectory = $cacheDirectory;
    }

    public function get($key, $default = null)
    {
        if (false === $this->has($key)) {
            return $default;
        }

        $serialized_content = file_get_contents($this->getCacheFile($key));

        if (false === $serialized_content) {
            return $default;
        }

        return unserialize($serialized_content);
    }

    public function set($key, $value, $ttl = null)
    {
        $filePath = $this->getCacheFile($key);

        file_put_contents($filePath, serialize($value));
    }

    public function delete($key)
    {
        if (false === $this->has($key)) {
            return;
        }

        unlink($this->getCacheFile($key));
    }

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

    public function getMultiple($keys, $default = null)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    public function setMultiple($values, $ttl = null)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    public function deleteMultiple($keys)
    {
        throw new Exception('`' . __METHOD__ . '` not implemented yet.');
    }

    public function has($key)
    {
        return file_exists($this->getCacheFile($key));
    }

    private function getCacheFile(string $key)
    {
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . $key . self::CACHE_SUFFIX;
    }
}
