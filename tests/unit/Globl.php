<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Type;

/**
 * @small
 */
final class Globl extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));

        self::assertIsObject(Wasm\Globl::new($store, $globaltype, 42));
        self::assertIsObject(Wasm\Globl::new($store, $globaltype, Wasm\Val::new(-42)));

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I64));

        self::assertIsObject(Wasm\Globl::new($store, $globaltype, 42));
        self::assertIsObject(Wasm\Globl::new($store, $globaltype, Wasm\Val::new(-42)));

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F32));

        self::assertIsObject(Wasm\Globl::new($store, $globaltype, 42.3));
        self::assertIsObject(Wasm\Globl::new($store, $globaltype, Wasm\Val::new(-42.3)));

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F64));

        self::assertIsObject(Wasm\Globl::new($store, $globaltype, 42.3));
        self::assertIsObject(Wasm\Globl::new($store, $globaltype, Wasm\Val::new(-42.3)));

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_ANYREF));

        try {
            self::assertIsObject(Wasm\Globl::new($store, $globaltype, 42));

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
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

        self::assertIsObject(new Wasm\Globl($global));

        try {
            new Wasm\Globl(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Globl(\wasm_config_new());

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
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Wasm\Globl::new($store, $globaltype, 42);

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

        self::assertSame((new Wasm\Globl($global))->inner(), $global);
    }

    /**
     * @test
     */
    public function type(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Wasm\Globl::new($store, $globaltype, 42);

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
        $global = Wasm\Globl::new($store, $globaltype, 42);

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
        $global = Wasm\Globl::new($store, $globaltype, 42);

        self::assertEquals(42, $global->get()->value());
    }

    /**
     * @test
     */
    public function set(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        $valtypeI32 = Type\ValType::new(Type\ValType::KIND_I32);
        $globaltypeI32 = Type\GlobalType::new($valtypeI32, Type\GlobalType::MUTABILITY_VAR);
        $globalI32 = Wasm\Globl::new($store, $globaltypeI32, 42);

        self::assertNull($globalI32->set(1337));
        self::assertEquals(1337, $globalI32->get()->value());

        $valtypeI64 = Type\ValType::new(Type\ValType::KIND_I64);
        $globaltypeI64 = Type\GlobalType::new($valtypeI64, Type\GlobalType::MUTABILITY_VAR);
        $globalI64 = Wasm\Globl::new($store, $globaltypeI64, 42);

        self::assertNull($globalI64->set(1337));
        self::assertEquals(1337, $globalI64->get()->value());

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F32), Type\GlobalType::MUTABILITY_VAR);
        $global = Wasm\Globl::new($store, $globaltype, 42);

        self::assertNull($global->set(1337));
        self::assertEquals(1337, $global->get()->value());

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F64), Type\GlobalType::MUTABILITY_VAR);
        $global = Wasm\Globl::new($store, $globaltype, 42);

        self::assertNull($global->set(1337));
        self::assertEquals(1337, $global->get()->value());

        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Wasm\Globl::new($store, $globaltype, 42);

        try {
            $global->set(1337);

            self::fail();
        } catch (Exception\RuntimeException) {
        }
    }

    /**
     * @test
     */
    public function same(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $globaltype = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $global = Wasm\Globl::new($store, $globaltype, 42);
        $copy = clone $global;
        $otherCopy = clone $copy;

        self::assertTrue($global->same($copy));
        self::assertTrue($copy->same($copy));
        self::assertTrue($global->same($otherCopy));

        $other = Wasm\Globl::new($store, $globaltype, 42);

        self::assertFalse($global->same($other));
        self::assertFalse($copy->same($other));
    }
}
