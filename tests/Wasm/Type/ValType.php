<?php declare(strict_types=1);

namespace Wasm\Tests\Units\Type;

use atoum\atoum;
use Wasm;
use Wasm\Type;

class ValType extends atoum\test
{
    public function testNew()
    {
        $this
            ->object(Type\ValType::new(Type\ValType::KIND_I32))
            ->object(Type\ValType::new(Type\ValType::KIND_I64))
            ->object(Type\ValType::new(Type\ValType::KIND_F32))
            ->object(Type\ValType::new(Type\ValType::KIND_F64))
            ->object(Type\ValType::new(Type\ValType::KIND_ANYREF))
            ->object(Type\ValType::new(Type\ValType::KIND_FUNCREF))
            ->given($kind = 99)
            ->then
                ->exception(fn () => Type\ValType::new($kind))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
        ;
    }

    public function testConstruct()
    {
        $this
            ->given($wasmValtype = \wasm_valtype_new(WASM_I32))
            ->then
                ->object($valtype = new Type\ValType($wasmValtype))
                ->resource($valtype->inner())->isIdenticalTo($wasmValtype)
        ;
    }

    public function testDestruct()
    {
        $this
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_I32))
            ->then
                ->variable($valtype->__destruct())->isNull()
                ->variable($valtype->__destruct())->isNull()
        ;
    }

    public function testIsNum()
    {
        $this
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_I32))
            ->then
                ->boolean($valtype->isNum())->isTrue()
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_ANYREF))
            ->then
                ->boolean($valtype->isNum())->isFalse()
        ;
    }

    public function testIsRef()
    {
        $this
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_ANYREF))
            ->then
                ->boolean($valtype->isRef())->isTrue()
            ->given($valtype = Type\ValType::new(Type\ValType::KIND_I32))
            ->then
                ->boolean($valtype->isRef())->isFalse()
        ;
    }

    public function testKind()
    {
        $this
            ->given(
                $kind = Type\ValType::KIND_ANYREF,
                $valtype = Type\ValType::new($kind)
            )
            ->then
                ->integer($valtype->kind())->isEqualTo($kind)
        ;
    }
}
