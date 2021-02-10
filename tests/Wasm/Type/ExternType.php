<?php declare(strict_types=1);

namespace Wasm\Tests\Units\Type;

use atoum\atoum;
use Wasm;
use Wasm\Type;
use Wasm\Vec;

class ExternType extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->given(
                $functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType()),
                $externtype = wasm_functype_as_externtype($functype),
            )
            ->then
                ->object(new Type\ExternType($externtype))
                ->exception(fn () => new Type\ExternType(42))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
                ->exception(fn () => new Type\ExternType($functype))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
        ;
    }

    public function testDestruct()
    {
        $this
            ->given(
                $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType()),
                $externtype = $functype->asExternType(),
            )
            ->then
                ->variable($externtype->__destruct())->isNull()
                ->variable($externtype->__destruct())->isNull()
        ;
    }

    public function testAsGlobalType()
    {
        $this
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
                $externtype = $globaltype->asExternType(),
            )
            ->then
                ->object($externtype->asGlobalType())->isInstanceOf(Type\GlobalType::class)
            ->given(
                $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType()),
                $externtype = $functype->asExternType(),
            )
            ->then
                ->exception(fn () => $externtype->asGlobalType())
                    ->isInstanceOf(Wasm\Exception\RuntimeException::class)
        ;
    }

    public function testAsFuncType()
    {
        $this
            ->given(
                $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType()),
                $externtype = $functype->asExternType(),
            )
            ->then
                ->object($externtype->asFuncType())->isInstanceOf(Type\FuncType::class)
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
                $externtype = $globaltype->asExternType(),
            )
            ->then
                ->exception(fn () => $externtype->asFuncType())
                    ->isInstanceOf(Wasm\Exception\RuntimeException::class)
        ;
    }
}
