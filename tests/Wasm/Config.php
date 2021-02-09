<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Config extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->object(new Wasm\Config())
        ;
    }

    public function testDestruct()
    {
        $this
            ->given($config = new Wasm\Config())
            ->then
                ->variable($config->__destruct())->isNull()
                ->variable($config->__destruct())->isNull()
        ;
    }

    public function testSetCompiler()
    {
        $this
            ->given($config = new Wasm\Config())
            ->then
                ->boolean($config->setCompiler(Wasm\Config::COMPILER_CRANELIFT))->isTrue()
                ->boolean($config->setCompiler(Wasm\Config::COMPILER_LLVM))->isTrue()
                ->boolean($config->setCompiler(Wasm\Config::COMPILER_SINGLEPASS))->isTrue()
            ->given($compiler = 99)
            ->then
                ->exception(fn () => $config->setCompiler(($compiler)))
                    ->isInstanceOf(Wasm\Exception\InvalidArgumentException::class)
        ;
    }

    public function testSetEngine()
    {
        $this
            ->given($config = new Wasm\Config())
            ->then
                ->boolean($config->setEngine(Wasm\Config::ENGINE_JIT))->isTrue()
                ->boolean($config->setEngine(Wasm\Config::ENGINE_NATIVE))->isTrue()
                ->boolean($config->setEngine(Wasm\Config::ENGINE_OBJECT_FILE))->isTrue()
            ->given($engine = 99)
            ->then
                ->exception(fn () => $config->setEngine(($engine)))
                    ->isInstanceOf(Wasm\Exception\InvalidArgumentException::class)
        ;
    }
}