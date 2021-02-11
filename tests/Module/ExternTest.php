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
final class ExternTest extends TestCase
{
    /**
     * @test
     */
    public function construct(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();

        self::assertIsObject(new Module\Extern($exports[0]));

        try {
            new Module\Extern(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {}

        try {
            new Module\Extern(\wasm_config_new());

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
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $extern = new Module\Extern($exports[0]);

        self::assertNull($extern->__destruct());
        self::assertNull($extern->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $export = $exports[0];
        $extern = new Module\Extern($export);

        self::assertSame($extern->inner(), $export);
    }

    /**
     * @test
     */
    public function asFunc(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $extern = new Module\Extern($exports[0]);

        self::assertIsObject($extern->asFunc());
    }

    /**
     * @test
     */
    public function asGlobal(): void
    {
        $wat = '(module (global (export "global") i32 (i32.const 1)))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $extern = new Module\Extern($exports[0]);

        self::assertIsObject($extern->asGlobal());
    }

    /**
     * @test
     */
    public function kind(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $extern = new Module\Extern($exports[0]);

        self::assertEquals(Type\ExternType::KIND_FUNC, $extern->kind());
    }

    /**
     * @test
     */
    public function type(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $extern = new Module\Extern($exports[0]);

        self::assertIsObject($extern->type());
    }
}