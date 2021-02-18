<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;

/**
 * @small
 */
final class Config extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        self::assertIsObject(Wasm\Config::new());
        self::assertIsObject(Wasm\Config::new(Wasm\Config::COMPILER_CRANELIFT));
        self::assertIsObject(Wasm\Config::new(Wasm\Config::COMPILER_CRANELIFT, Wasm\Config::ENGINE_JIT));
        self::assertIsObject(Wasm\Config::new(null, Wasm\Config::ENGINE_JIT));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $config = \wasm_config_new();

        self::assertIsObject(new Wasm\Config($config));

        try {
            new Wasm\Config(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Config(\wasm_valtype_new(WASM_I32));

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $config = Wasm\Config::new();

        self::assertNull($config->__destruct());
        self::assertNull($config->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $config = \wasm_config_new();

        self::assertSame((new Wasm\Config($config))->inner(), $config);
    }

    /**
     * @test
     */
    public function setCompiler(): void
    {
        $config = Wasm\Config::new();

        self::assertTrue($config->setCompiler(Wasm\Config::COMPILER_CRANELIFT));
        self::assertTrue($config->setCompiler(Wasm\Config::COMPILER_LLVM));
        self::assertTrue($config->setCompiler(Wasm\Config::COMPILER_SINGLEPASS));

        try {
            $config->setCompiler(99);
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function setEngine(): void
    {
        $config = Wasm\Config::new();

        self::assertTrue($config->setEngine(Wasm\Config::ENGINE_JIT));
        self::assertTrue($config->setEngine(Wasm\Config::ENGINE_NATIVE));
        self::assertTrue($config->setEngine(Wasm\Config::ENGINE_OBJECT_FILE));

        try {
            $config->setEngine(99);
        } catch (Exception\InvalidArgumentException) {
        }
    }
}
