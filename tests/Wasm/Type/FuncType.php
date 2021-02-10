<?php declare(strict_types=1);

namespace Wasm\Tests\Units\Type;

use atoum\atoum;
use Wasm;
use Wasm\Type;
use Wasm\Vec;

class FuncType extends atoum\test
{
    public function testNew()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
            )
            ->then
                ->object(Type\FuncType::new($params, $results))
        ;
    }

    public function testConstruct()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
                $wasmFunctype = \wasm_functype_new($params, $results),
            )
            ->then
                ->object($functype = new Type\FuncType($wasmFunctype))
                ->resource($functype->inner())->isIdenticalTo($wasmFunctype)
                ->exception(fn () => new Type\FuncType(42))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
                ->exception(fn () => new Type\FuncType(\wasm_valtype_new(WASM_I32)))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
        ;
    }

    public function testClone()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
                $functype = Type\FuncType::new($params, $results),
                $functypeCopy = clone $functype,
            )
            ->then
                ->resource($functype->inner())->isNotIdenticalTo($functypeCopy->inner())
        ;
    }

    public function testDestruct()
    {
        $this
            ->given($functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType()))
            ->then
                ->variable($functype->__destruct())->isNull()
                ->variable($functype->__destruct())->isNull()
        ;
    }

    public function testAsExternType()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
                $functype = Type\FuncType::new($params, $results),
            )
            ->then
                ->object($externtype = $functype->asExternType())->isInstanceOf(Type\ExternType::class)
                ->integer($externtype->kind())->isEqualTo(Type\ExternType::KIND_FUNC)
        ;
    }

    public function testParams()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
                $functype = Type\FuncType::new($params, $results),
            )
            ->then
                ->object($functype->params())->isInstanceOf(Vec\ValType::class)
        ;
    }

    public function testResults()
    {
        $this
            ->given(
                $params = new Vec\ValType(),
                $results = new Vec\ValType(),
                $functype = Type\FuncType::new($params, $results),
            )
            ->then
                ->object($functype->results())->isInstanceOf(Vec\ValType::class)
        ;
    }
}
