<?php

declare(strict_types=1);

namespace Wasm\Examples;

use Wasm;
use Wasm\Module;

/**
 * @medium
 */
final class Hello extends Example
{
    private static $called = false;

    /**
     * @test
     */
    public function main()
    {
        // Initializing...
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        // Loading binary...
        $wasm = file_get_contents($this->module('wasm'));

        // Compiling module...
        $module = Wasm\Module::new($store, $wasm);

        $functype = Wasm\Type\FuncType::new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());
        $func = Wasm\Module\Func::new($store, $functype, [self::class, 'hello_callback']);

        // Instantiating module...
        $extern = $func->asExtern();
        $externs = new Wasm\Vec\Extern([$extern->inner()]);
        $instance = Wasm\Module\Instance::new($store, $module, $externs);

        // Extracting export...
        $exports = $instance->exports();
        $run = (new Module\Extern($exports[0]))->asFunc();

        // Calling export...
        $args = new Wasm\Vec\Val();
        $run($args);

        self::assertTrue(self::$called, 'Callback was not called');
    }

    public static function hello_callback(): void
    {
        self::$called = true;
    }
}
