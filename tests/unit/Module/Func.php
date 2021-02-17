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
final class Func extends TestCase
{
    /**
     * @test
     */
    public function new(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());

        self::assertIsObject(Module\Func::new($store, $functype, fn () => null));
    }

    /**
     * @test
     */
    public function construct(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $functype = \wasm_functype_new(new Vec\ValType(), new Vec\ValType());
        $func = \wasm_func_new($store, $functype, fn () => null);

        self::assertIsObject(new Module\Func($func));

        try {
            new Module\Func(42);

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }

        try {
            new Module\Func(\wasm_config_new());

            self::fail();
        } catch (Exception\InvalidArgumentException) {
        }
    }

    /**
     * @test
     */
    public function destruct(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertNull($func->__destruct());
        self::assertNull($func->__destruct());
    }

    /**
     * @test
     */
    public function inner(): void
    {
        $engine = \wasm_engine_new();
        $store = \wasm_store_new($engine);
        $functype = \wasm_functype_new(new Vec\ValType(), new Vec\ValType());
        $func = \wasm_func_new($store, $functype, fn () => null);

        self::assertSame((new Module\Func($func))->inner(), $func);
    }

    /**
     * @test
     */
    public function paramArity(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertEquals(0, $func->paramArity());

        $type = Type\ValType::new(Type\ValType::KIND_I32);
        $inner = $type->inner();
        $params = new Vec\ValType([$inner]);
        $results = new Vec\ValType();
        $functype = Type\FuncType::new($params, $results);
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertEquals(1, $func->paramArity());
    }

    /**
     * @test
     */
    public function resultarity(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertEquals(0, $func->resultArity());

        $type = Type\ValType::new(Type\ValType::KIND_I32);
        $inner = $type->inner();
        $params = new Vec\ValType();
        $results = new Vec\ValType([$inner]);
        $functype = Type\FuncType::new($params, $results);
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertEquals(1, $func->resultArity());
    }

    /**
     * @test
     */
    public function type(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertIsObject($func->type());
    }

    /**
     * @test
     */
    public function asExtern(): void
    {
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $functype = Type\FuncType::new(new Vec\ValType(), new Vec\ValType());
        $func = Module\Func::new($store, $functype, fn () => null);

        self::assertIsObject($func->asExtern());
    }

    /**
     * @test
     */
    public function call(): void
    {
        $wat = '(module (func (export "run")))';
        $wasm = Wat::wasm($wat);
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);
        $module = Module::new($store, $wasm);
        $instance = Module\Instance::new($store, $module, new Vec\Extern());
        $exports = $instance->exports();
        $func = (new Module\Extern($exports[0]))->asFunc();

        self::assertIsObject($func());
    }
}
