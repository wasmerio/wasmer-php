<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm\Exception;
use Wasm\Type;

/**
 * @small
 */
final class GlobalType extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);

        self::assertIsObject(Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR));

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);

        self::assertIsObject(Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_CONST));

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $mutability = 99;

        try {
            Type\GlobalType::new($valtype, $mutability);

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
        $globaltype = \wasm_globaltype_new($valtype, WASM_CONST);

        self::assertIsObject(new Type\GlobalType($globaltype));

        try {
            new Type\GlobalType(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Type\GlobalType(\wasm_config_new());

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
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_CONST);

        self::assertNull($globaltype->__destruct());
        self::assertNull($globaltype->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $valtype = \wasm_valtype_new(WASM_I32);
        $globaltype = \wasm_globaltype_new($valtype, WASM_CONST);

        self::assertSame((new Type\GlobalType($globaltype))->inner(), $globaltype);
    }

    /**
     * @test
     */
    public function asExternType(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_CONST);

        self::assertIsObject($globaltype->asExternType());
    }

    /**
     * @test
     */
    public function mutability(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $mutability = Type\GlobalType::MUTABILITY_VAR;
        $globaltype = Type\GlobalType::new($valtype, $mutability);

        self::assertEquals($mutability, $globaltype->mutability());

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $mutability = Type\GlobalType::MUTABILITY_CONST;
        $globaltype = Type\GlobalType::new($valtype, $mutability);

        self::assertEquals($mutability, $globaltype->mutability());
    }

    /**
     * @test
     */
    public function testContent(): void
    {
        $kind = Type\ValType::KIND_I32;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());

        $kind = Type\ValType::KIND_I64;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());

        $kind = Type\ValType::KIND_F32;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());

        $kind = Type\ValType::KIND_F64;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());

        $kind = Type\ValType::KIND_ANYREF;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());

        $kind = Type\ValType::KIND_FUNCREF;
        $valtype = Type\ValType::new($kind);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);

        self::assertIsObject($globaltype->content());
        self::assertEquals($kind, $globaltype->content()->kind());
    }
}
