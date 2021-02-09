<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Engine extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->object(Wasm\Engine::new())
            ->given($config = Wasm\Config::new())
            ->then
                ->object(Wasm\Engine::new($config))
        ;

        // TODO(jubianchi): Enable all compilers
        $compilers = [Wasm\Config::COMPILER_CRANELIFT/*, Wasm\Config::COMPILER_LLVM, Wasm\Config::COMPILER_SINGLEPASS*/];
        $engines = [Wasm\Config::ENGINE_JIT, Wasm\Config::ENGINE_NATIVE, Wasm\Config::ENGINE_OBJECT_FILE];

        foreach ($compilers as $compiler) {
            foreach ($engines as $engine) {
                $this
                    ->given(
                        $config = Wasm\Config::new(),
                        $config->setCompiler($compiler),
                        $config->setEngine($engine),
                    )
                    ->then
                        ->object(Wasm\Engine::new($config))
                ;
            }
        }
    }

    public function testDestruct()
    {
        $this
            ->given($engine = Wasm\Engine::new())
            ->then
                ->variable($engine->__destruct())->isNull()
                ->variable($engine->__destruct())->isNull()
        ;
    }
}