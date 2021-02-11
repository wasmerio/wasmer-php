<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Module;
use Wasm\Type;
use Wasm\Vec;
use Wasm\Wat;

/**
 * @small
 */
final class GloblTest extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));

        self::assertIsObject(Module\Globl::new($store, $globaltype, 42));
        self::assertIsObject(Module\Globl::new($store, $globaltype, 42.3));
        self::assertIsObject(Module\Globl::new($store, $globaltype, Module\Val::new(-1)));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $valtype = \wasm_valtype_new(WASM_I32);
        $globaltype = \wasm_globaltype_new($valtype, WASM_CONST);
        $global = \wasm_global_new($store, $globaltype, \wasm_val_i32(42));

        self::assertIsObject(new Module\Globl($global));

        try {
            new Module\Globl(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {}

        try {
            new Module\Globl(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {}
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Module\Globl::new($store, $globaltype, 42);

        self::assertNull($global->__destruct());
        self::assertNull($global->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $valtype = \wasm_valtype_new(WASM_I32);
        $globaltype = \wasm_globaltype_new($valtype, WASM_CONST);
        $global = \wasm_global_new($store, $globaltype, \wasm_val_i32(42));

        self::assertSame((new Module\Globl($global))->inner(), $global);
    }

    /**
     * @test
     */
    public function type(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Module\Globl::new($store, $globaltype, 42);

        self::assertIsObject($global->type());
    }

    /**
     * @test
     */
    public function asExtern(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Module\Globl::new($store, $globaltype, 42);

        self::assertIsObject($global->asExtern());
    }

    /**
     * @test
     */
    public function get(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Module\Globl::new($store, $globaltype, 42);

        self::assertEquals(42, $global->get()->value());
    }

    /**
     * @test
     */
    public function set(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32), Type\GlobalType::MUTABILITY_VAR);
        $global = Module\Globl::new($store, $globaltype, 42);

        self::assertNull($global->set(1337));
        self::assertEquals(1337, $global->get()->value());

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Module\Globl::new($store, $globaltype, 42);

        try {
            $global->set(1337);

            self::fail();
        } catch (Exception\RuntimeException) {}
    }
}