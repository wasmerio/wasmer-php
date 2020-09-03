<?php

declare(strict_types = 1);

namespace Wasm\Tests\Units\Cache;

use Wasm as LUT;
use Wasm\Cache\Filesystem as SUT;
use Wasm\Tests\Suite;

class Filesystem extends Suite
{
    const FILE_PATH = __DIR__ . '/../tests.wasm';

    public function test_constructor()
    {
        $this
            ->given($directory = $this->directory())
            ->when($result = new SUT($directory))
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Cache\CacheInterface::class);
    }

    public function test_constructor_directory_does_not_exist()
    {
        $this
            ->given($directory = 'foo')
            ->exception(
                function () use ($directory) {
                    new SUT($directory);
                }
            )
                ->isInstanceOf(LUT\Cache\Exception::class)
                ->hasMessage("The cache directory `$directory` is not a directory.");
    }

    public function test_get()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $module = new LUT\Module(self::FILE_PATH),
                $cache->set($key, $module)
            )
            ->when($result = $cache->get($key))
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Module::class)
                ->integer($result->instantiate()->sum(1, 2))
                    ->isEqualTo(3);
    }

    public function test_get_not_found()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $default = 42
            )
            ->when($result = $cache->get($key, $default))
            ->then
                ->variable($result)
                    ->isEqualTo($default);
    }

    public function test_get_failed_to_deserialize()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $default = 42,
                $this->function->file_get_contents = 'foo'
            )
            ->when($result = $cache->get($key, $default))
            ->then
                ->variable($result)
                    ->isEqualTo($default);
    }

    public function test_set()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $module = new LUT\Module(self::FILE_PATH)
            )
            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $cache->set($key, $module))
            ->then
                ->variable($result)
                    ->isNull()

            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $cache->get($key))
            ->then
                ->object($result)
                    ->isInstanceOf(LUT\Module::class)
                ->integer($result->instantiate()->sum(1, 2))
                    ->isEqualTo(3);
    }

    public function test_set_not_a_module()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__
            )
            ->exception(
                function () use ($cache, $key) {
                    $cache->set($key, 'foo');
                }
            )
                ->isInstanceOf(LUT\Cache\InvalidArgumentException::class)
                ->hasMessage('The cache can only store `' . LUT\Module::class . '` instances.');
    }

    public function test_delete()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $module = new LUT\Module(self::FILE_PATH),
                $cache->set($key, $module)
            )
            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $cache->delete($key))
            ->then
                ->variable($result)
                    ->isNull()

            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function test_delete_not_found()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__
            )
            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isFalse()

            ->when($result = $cache->delete($key))
            ->then
                ->variable($result)
                    ->isNull()

            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function test_clear()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key1 = __METHOD__ . '@1',
                $key2 = __METHOD__ . '@2',
                $module = new LUT\Module(self::FILE_PATH),
                $cache->set($key1, $module),
                $cache->set($key2, $module)
            )
            ->when($result = $cache->has($key1) && $cache->has($key2))
            ->then
                ->boolean($result)
                    ->isTrue()

            ->when($result = $cache->clear())
            ->then
                ->variable($result)
                    ->isNull()

            ->when($result = $cache->has($key1) || $cache->has($key2))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    public function test_has()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $this->function->file_exists = true
            )
            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isTrue();
    }

    public function test_has_not()
    {
        $this
            ->given(
                $directory = $this->directory(),
                $cache = new SUT($directory),
                $key = __METHOD__,
                $this->function->file_exists = false
            )
            ->when($result = $cache->has($key))
            ->then
                ->boolean($result)
                    ->isFalse();
    }

    private function directory()
    {
        do {
            $directory =
                sys_get_temp_dir() . DIRECTORY_SEPARATOR .
                'wasmer-php' . DIRECTORY_SEPARATOR .
                'tests' . DIRECTORY_SEPARATOR .
                uniqid() . '-' . uniqid();
        } while(true === is_dir($directory));

        mkdir($directory, 0777, true);

        return $directory;
    }
}
