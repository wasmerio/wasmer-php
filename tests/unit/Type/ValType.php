<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm\Exception;
use Wasm\Type;

/**
 * @small
 */
final class ValType extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_I32));
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_I64));
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_F32));
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_F64));
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_ANYREF));
        self::assertIsObject(Type\ValType::new(Type\ValType::KIND_FUNCREF));

        try {
            Type\ValType::new(99);

            self::fail();
        } catch (Exception\RuntimeException) {
        }
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $valtype = \wasm_valtype_new(WASM_I32);

        self::assertIsObject(new Type\ValType($valtype));

        try {
            new Type\ValType(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Type\ValType(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);

        self::assertNull($valtype->__destruct());
        self::assertNull($valtype->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $valtype = \wasm_valtype_new(WASM_I32);

        self::assertSame((new Type\ValType($valtype))->inner(), $valtype);
    }

    /**
     * @test
     */
    public function isNum(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);

        self::assertTrue($valtype->isNum());

        $valtype = Type\ValType::new(Type\ValType::KIND_ANYREF);

        self::assertFalse($valtype->isNum());
    }

    /**
     * @test
     */
    public function isRef(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_ANYREF);

        self::assertTrue($valtype->isRef());

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);

        self::assertFalse($valtype->isRef());
    }

    /**
     * @test
     */
    public function kind(): void
    {
        $kind = Type\ValType::KIND_ANYREF;
        $valtype = Type\ValType::new($kind);

        self::assertEquals($kind, $valtype->kind());
    }
}
