<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Type;
use Wasm\Vec;

/**
 * @small
 */
final class FuncType extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $params = new Vec\ValType();
        $results = new Vec\ValType();

        self::assertIsObject(Type\FuncType::new($params, $results));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());

        self::assertIsObject(new Type\FuncType($functype));

        try {
            new Type\FuncType(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Type\FuncType(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function clone(): void
    {
        $params = new Vec\ValType();
        $results = new Vec\ValType();
        $functype = Type\FuncType::new($params, $results);
        $copy = clone $functype;

        self::assertNotEquals($functype->inner(), $copy->inner());
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());

        self::assertNull($functype->__destruct());
        self::assertNull($functype->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $functype = \wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());

        self::assertSame((new Type\FuncType($functype))->inner(), $functype);
    }

    /**
     * @test
     */
    public function asExternType(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());

        self::assertIsObject($functype->asExternType());
    }

    /**
     * @test
     */
    public function params(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());

        self::assertIsObject($functype->params());
    }

    /**
     * @test
     */
    public function results(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());

        self::assertIsObject($functype->results());
    }
}
