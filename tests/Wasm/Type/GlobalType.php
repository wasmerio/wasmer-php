<?php declare(strict_types=1);

namespace Wasm\Tests\Units\Type;

use atoum\atoum;
use Wasm;
use Wasm\Type;

class GlobalType extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_I32))
            ->then
                ->object(Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR))
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_I32))
            ->then
                ->object(Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_CONST))
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $mutability = 99,
            )
            ->then
                ->exception(fn () => Type\GlobalType::new($valtype, $mutability))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
        ;
    }

    public function testDestruct()
    {
        $this
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->variable($globaltype->__destruct())->isNull()
                ->variable($globaltype->__destruct())->isNull()
        ;
    }

    public function testMutability()
    {
        $this
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $mutability = Type\GlobalType::MUTABILITY_VAR,
                $globaltype = Type\GlobalType::new($valtype, $mutability),
            )
            ->then
                ->integer($globaltype->mutability())->isEqualTo($mutability)
            ->given(
                $valtype = Type\ValType::new(Type\ValType::KIND_I32),
                $mutability = Type\GlobalType::MUTABILITY_CONST,
                $globaltype = Type\GlobalType::new($valtype, $mutability),
            )
            ->then
                ->integer($globaltype->mutability())->isEqualTo($mutability)
        ;
    }

    public function testContent()
    {
        $this
            ->given(
                $kind = Type\ValType::KIND_I32,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
            ->given(
                $kind = Type\ValType::KIND_I64,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
            ->given(
                $kind = Type\ValType::KIND_F32,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
            ->given(
                $kind = Type\ValType::KIND_F64,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
            ->given(
                $kind = Type\ValType::KIND_ANYREF,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
            ->given(
                $kind = Type\ValType::KIND_FUNCREF,
                $valtype = Type\ValType::new($kind),
                $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR),
            )
            ->then
                ->object($globaltype->content())
                ->integer($globaltype->content()->kind())->isEqualTo($kind)
        ;
    }
}
