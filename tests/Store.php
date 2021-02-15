<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;

/**
 * @small
 */
final class Store extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $engine = Wasm\Engine::new();

        self::assertIsObject(Wasm\Store::new($engine));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);

        self::assertIsObject(new Wasm\Store($store));

        try {
            new Wasm\Store(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Store(\wasm_config_new());

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
        $store = Wasm\Store::new($engine);

        self::assertNull($store->__destruct());
        self::assertNull($store->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);

        self::assertSame((new Wasm\Store($store))->inner(), $store);
    }
}
