<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Engine extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->given($config = null)
            ->then
                ->object(new Wasm\Engine($config))
            ->given($config = new Wasm\Config())
            ->then
                ->object(new Wasm\Engine($config))
        ;

        // TODO(jubianchi): Enable all compilers
        $compilers = [Wasm\Config::COMPILER_CRANELIFT/*, Wasm\Config::COMPILER_LLVM, Wasm\Config::COMPILER_SINGLEPASS*/];
        $engines = [Wasm\Config::ENGINE_JIT, Wasm\Config::ENGINE_NATIVE, Wasm\Config::ENGINE_OBJECT_FILE];

        foreach ($compilers as $compiler) {
            foreach ($engines as $engine) {
                $this
                    ->given(
                        $config = new Wasm\Config(),
                        $config->setCompiler($compiler),
                        $config->setEngine($engine),
                    )
                    ->then
                        ->object(new Wasm\Engine($config))
                ;
            }
        }
    }

    public function testDestruct()
    {
        $this
            ->given($engine = new Wasm\Engine())
            ->then
                ->variable($engine->__destruct())->isNull()
                ->variable($engine->__destruct())->isNull()
        ;
    }
}