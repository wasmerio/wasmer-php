<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Wat;

/**
 * @small
 */
final class Module extends TestCase
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

        self::assertIsObject(Wasm\Module::new($store, $wasm));
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

        self::assertIsObject(new Wasm\Module($module));

        try {
            new Wasm\Module(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Wasm\Module(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
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
        $module = Wasm\Module::new($store, $wasm);

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

        self::assertSame((new Wasm\Module($module))->inner(), $module);
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
        $module = Wasm\Module::new($store, $wasm);

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
        $module = Wasm\Module::new($store, $wasm);

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
        $module = Wasm\Module::new($store, $wasm);

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
        $module = Wasm\Module::new($store, $wasm);

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
        $module = Wasm\Module::new($store, $wasm);
        $serialized = $module->serialize();

        self::assertIsObject(Wasm\Module::deserialize($store, $serialized));
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

        self::assertTrue(Wasm\Module::validate($store, $wasm));

        try {
            Wasm\Module::validate($store, 'invalid');

            self::fail();
        } catch (Exception\RuntimeException) {
        }
    }
}
