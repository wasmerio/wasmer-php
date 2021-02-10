<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;
use Wasm\Vec;

class Module extends atoum\test
{
    public function testNew()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
            )
            ->then
                ->object(Wasm\Module::new($store, $wasm))
        ;
    }

    public function testConstruct()
    {
        $this
            ->given(
                $engine = \wasm_engine_new(),
                $store = \wasm_store_new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = \wasm_module_new($store, $wasm),
            )
            ->then
                ->object(new Wasm\Module($module))
                ->exception(fn () => new Wasm\Module(42))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
                ->exception(fn () => new Wasm\Module(\wasm_valtype_new(WASM_I32)))
                    ->isInstanceOf(Wasm\Exception\invalidArgumentException::class)
        ;
    }

    public function testDestruct()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
            )
            ->then
                ->variable($module->__destruct())->isNull()
                ->variable($module->__destruct())->isNull()
        ;
    }

    public function testExports()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
            )
            ->then
                ->object($module->exports())->isInstanceOf(Vec\ExportType::class)
        ;
    }

    public function testImports()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
            )
            ->then
                ->object($module->imports())->isInstanceOf(Vec\ImportType::class)
        ;
    }

    public function testName()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
            )
            ->then
                ->string($module->name())->isEmpty()
            ->given($name = 'test')
            ->then
                ->string($module->name($name))->isEmpty()
                ->string($module->name())->isEqualTo($name)
            ->given($newName = 'new')
            ->then
                ->string($module->name($newName))->isEqualTo($name)
                ->string($module->name())->isEqualTo($newName)
        ;
    }

    public function testSerialize()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
            )
            ->then
                ->string($module->serialize())->isNotEmpty()
        ;
    }

    public function testDeserialize()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
                $module = Wasm\Module::new($store, $wasm),
                $ser = $module->serialize(),
            )
            ->then
                ->object(Wasm\Module::deserialize($store, $ser))->isInstanceOf(Wasm\Module::class)
        ;
    }

    public function testValidate()
    {
        $this
            ->given(
                $engine = Wasm\Engine::new(),
                $store = Wasm\Store::new($engine),
                $wasm = Wasm\Wat::wasm('(module)'),
            )
            ->then
                ->boolean(Wasm\Module::validate($store, $wasm))->isTrue()
        ;
    }
}