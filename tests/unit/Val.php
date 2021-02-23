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
final class Val extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $value = Wasm\Val::new(\wasm_val_i32(42));
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I32, $value->kind());
        self::assertEquals(42, $value->value());

        $value = Wasm\Val::new(42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I32, $value->kind());
        self::assertEquals(42, $value->value());

        $value = Wasm\Val::new(PHP_INT_MAX);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I64, $value->kind());
        self::assertEquals(PHP_INT_MAX, $value->value());

        $value = Wasm\Val::new(-42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I32, $value->kind());
        self::assertEquals(-42, $value->value());

        $value = Wasm\Val::new(PHP_INT_MIN);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I64, $value->kind());
        self::assertEquals(PHP_INT_MIN, $value->value());

        $value = Wasm\Val::new((float) 1);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F32, $value->kind());
        self::assertEquals((float) 1, $value->value());

        $value = Wasm\Val::new(3.41e+38);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F64, $value->kind());
        self::assertEquals(3.41e+38, $value->value());

        $value = Wasm\Val::new((float) -1);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F32, $value->kind());
        self::assertEquals((float) -1, $value->value());

        $value = Wasm\Val::new(-3.41e+38);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F64, $value->kind());
        self::assertEquals(-3.41e+38, $value->value());

        try {
            self::assertIsObject(Wasm\Val::new('foo'));

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function newI32(): void
    {
        $value = Wasm\Val::newI32(42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I32, $value->kind());
        self::assertEquals(42, $value->value());

        $value = Wasm\Val::newI32(-42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I32, $value->kind());
        self::assertEquals(-42, $value->value());

        try {
            self::assertIsObject(Wasm\Val::newI32(PHP_INT_MAX));

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function newI64(): void
    {
        $value = Wasm\Val::newI64(PHP_INT_MAX);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I64, $value->kind());
        self::assertEquals(PHP_INT_MAX, $value->value());

        $value = Wasm\Val::newI64(PHP_INT_MIN);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_I64, $value->kind());
        self::assertEquals(PHP_INT_MIN, $value->value());
    }

    /**
     * @test
     */
    public function newF32(): void
    {
        $value = Wasm\Val::newF32((float) 42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F32, $value->kind());
        self::assertEquals((float) 42, $value->value());

        $value = Wasm\Val::newF32((float) -42);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F32, $value->kind());
        self::assertEquals((float) -42, $value->value());

        try {
            self::assertIsObject(Wasm\Val::newF32(3.41e+38));

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function newF64(): void
    {
        $value = Wasm\Val::newF64(3.41e+38);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F64, $value->kind());
        self::assertEquals(3.41e+38, $value->value());

        $value = Wasm\Val::newF64(-3.41e+38);
        self::assertIsObject($value);
        self::assertEquals(Type\ValType::KIND_F64, $value->kind());
        self::assertEquals(-3.41e+38, $value->value());
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $value = \wasm_val_i32(42);

        self::assertIsObject(new Wasm\Val($value));

        try {
            new Wasm\Val(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Val(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $value = Wasm\Val::new(42);

        self::assertNull($value->__destruct());
        self::assertNull($value->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $value = \wasm_val_i32(42);

        self::assertSame((new Wasm\Val($value))->inner(), $value);
    }
}
