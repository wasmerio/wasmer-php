<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Module;
use Wasm\Type;
use Wasm\Vec;
use Wasm\Wat;

/**
 * @small
 */
final class ModuleTest extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        self::assertIsObject(Module::new($store, $wasm));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $module = \wasm_module_new($store, $wasm);

        self::assertIsObject(new Module($module));

        try {
            new Module(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {}

        try {
            new Module(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {}
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertNull($module->__destruct());
        self::assertNull($module->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $module = \wasm_module_new($store, $wasm);

        self::assertSame((new Module($module))->inner(), $module);
    }

    /**
     * @test
     */
    public function exports(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertIsObject($module->exports());
    }

    /**
     * @test
     */
    public function imports(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertIsObject($module->imports());
    }

    /**
     * @test
     */
    public function name(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertEquals('', $module->name());
        self::assertEquals('', $module->name('name'));
        self::assertEquals('name', $module->name('new'));
        self::assertEquals('new', $module->name());
        self::assertEquals('new', $module->name(''));
        self::assertEquals('', $module->name());
    }

    /**
     * @test
     */
    public function serialize(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertNotEmpty($module->serialize());
    }

    /**
     * @test
     */
    public function deserialize(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $serialized = $module->serialize();

        self::assertIsObject(Module::deserialize($store, $serialized));
    }

    /**
     * @test
     */
    public function validate(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        self::assertTrue(Module::validate($store, $wasm));

        try {
            Module::validate($store, 'invalid');

            self::fail();
        } catch (Exception\RuntimeException) {}
    }
}