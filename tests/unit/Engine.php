<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;

/**
 * @small
 */
final class Engine extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        self::assertIsObject(Wasm\Engine::new());

        $config = Wasm\Config::new();

        self::assertIsObject(Wasm\Engine::new($config));

        // TODO(jubianchi): Enable all compilers
        $compilers = [Wasm\Config::COMPILER_CRANELIFT/*, Wasm\Config::COMPILER_LLVM, Wasm\Config::COMPILER_SINGLEPASS*/];
        $engines = [Wasm\Config::ENGINE_JIT, Wasm\Config::ENGINE_NATIVE, Wasm\Config::ENGINE_OBJECT_FILE];

        foreach ($compilers as $compiler) {
            foreach ($engines as $engine) {
                $config = Wasm\Config::new();
                $config->setCompiler($compiler);
                $config->setEngine($engine);

                self::assertIsObject(Wasm\Engine::new($config));
            }
        }
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $engine = \wasm_engine_new();

        self::assertIsObject(new Wasm\Engine($engine));

        try {
            new Wasm\Engine(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Engine(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $engine = Wasm\Engine::new();

        self::assertNull($engine->__destruct());
        self::assertNull($engine->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $engine = \wasm_engine_new();

        self::assertSame((new Wasm\Engine($engine))->inner(), $engine);
    }
}
