<?php

declare(strict_types=1);

namespace Wasm\Examples;

use Wasm;
use Wasm\Module\Extern;
use Wasm\Module\Val;
use Wasm\Wat;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * @medium
 */
final class Callback extends Example
{
    /**
     * @test
     */
    public function main()
    {
        // Initializing...
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        // Loading WAT...
        $wat = file_get_contents($this->module());

        // Loading binary...
        $wasm = Wat::wasm($wat);

        // Compiling module...
        $module = Wasm\Module::new($store, $wasm);

        $arg = Wasm\Type\ValType::new(Wasm\Type\ValType::KIND_I32);
        $result = Wasm\Type\ValType::new(Wasm\Type\ValType::KIND_I32);
        $printType = Wasm\Type\FuncType::new(
            new Wasm\Vec\ValType([$arg->inner()]),
            new Wasm\Vec\ValType([$result->inner()])
        );
        $print = Wasm\Module\Func::new($store, $printType, [self::class, 'print_callback']);

        $result = Wasm\Type\ValType::new(Wasm\Type\ValType::KIND_I32);
        $closureType = Wasm\Type\FuncType::new(
            new Wasm\Vec\ValType(),
            new Wasm\Vec\ValType([$result->inner()])
        );
        $closure = Wasm\Module\Func::new($store, $closureType, [self::class, 'closure']);

        // Instantiating module...
        $printExtern = $print->asExtern();
        $closureExtern = $closure->asExtern();
        $externs = new Wasm\Vec\Extern([$printExtern->inner(), $closureExtern->inner()]);
        $instance = Wasm\Module\Instance::new($store, $module, $externs);

        // Extracting export...
        $exports = $instance->exports();
        $run = (new Extern($exports[0]))->asFunc();

        // Calling export...
        $first = Wasm\Module\Val::newI32(3);
        $second = Wasm\Module\Val::newI32(4);
        $args = new Wasm\Vec\Val([
            $first->inner(),
            $second->inner(),
        ]);

        $results = $run($args);

        // Printing result...
        $result = new Val($results[0]);

        self::assertEquals(49, $result->value());
    }

    public static function print_callback(int $i32): int
    {
        // Calling back...
        self::assertEquals(7, $i32);

        return $i32;
    }

    public static function closure(): int
    {
        // Calling back closure...
        return 42;
    }
}
