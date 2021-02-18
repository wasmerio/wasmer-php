<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;
use Wasm\Module;
use Wasm\Vec;
use Wasm\Wat;

/**
 * @small
 */
final class Instance extends TestCase
{
    public function new(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);

        self::assertIsObject(Module\Instance::new($store, $module, new Vec\Extern()));
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
        $instance = \wasm_instance_new($store, $module, new Vec\Extern());

        self::assertIsObject(new Module\Instance($instance));

        try {
            new Module\Instance(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Module\Instance(\wasm_config_new());

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
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());

        self::assertNull($instance->__destruct());
        self::assertNull($instance->__destruct());
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
        $instance = \wasm_instance_new($store, $module, new Vec\Extern());

        self::assertSame((new Module\Instance($instance))->inner(), $instance);
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
        $instance = Module\Instance::new($store, $module, new Vec\Extern());

        self::assertIsObject($instance->exports());
    }
}
