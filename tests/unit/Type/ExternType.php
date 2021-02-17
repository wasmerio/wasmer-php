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
final class ExternType extends TestCase
{
    /**
     * @test
     */
    public function construct(): void
    {
        $functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
        $externtype = wasm_functype_as_externtype($functype);

        self::assertIsObject(new Type\ExternType($externtype));

        try {
            new Type\ExternType(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Type\ExternType(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $externtype = $functype->asExternType();

        self::assertNull($externtype->__destruct());
        self::assertNull($externtype->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $functype = \wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
        $externtype = \wasm_functype_as_externtype($functype);

        self::assertSame((new Type\ExternType($externtype))->inner(), $externtype);
    }

    /**
     * @test
     */
    public function asGlobalType(): void
    {
        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);
        $externtype = $globaltype->asExternType();

        self::assertIsObject($externtype->asGlobalType());

        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $externtype = $functype->asExternType();

        try {
            $externtype->asGlobalType();

            self::fail();
        } catch (Exception\RuntimeException) {
        }
    }

    /**
     * @test
     */
    public function asFuncType(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $externtype = $functype->asExternType();

        self::assertIsObject($externtype->asFuncType());

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);
        $externtype = $globaltype->asExternType();

        try {
            $externtype->asFuncType();

            self::fail();
        } catch (Exception\RuntimeException) {
        }
    }

    /**
     * @test
     */
    public function kind(): void
    {
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $externtype = $functype->asExternType();

        self::assertEquals(Type\ExternType::KIND_FUNC, $externtype->kind());

        $valtype = Type\ValType::new(Type\ValType::KIND_I32);
        $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);
        $externtype = $globaltype->asExternType();

        self::assertEquals(Type\ExternType::KIND_GLOBAL, $externtype->kind());
    }
}
