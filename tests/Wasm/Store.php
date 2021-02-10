<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Store extends atoum\test
{
    public function testNew()
    {
        $this
            ->given($engine = new Wasm\Engine())
            ->then
                ->object(Wasm\Store::new($engine))
        ;
    }

    public function testConstruct()
    {
        $this
            ->given(
                $engine = \wasm_engine_new(),
                $store = \wasm_store_new($engine),
            )
            ->then
                ->object(new Wasm\Store($store))
        ;
    }

    public function testDestruct()
    {
        $this
            ->given($store = Wasm\Store::new(new Wasm\Engine()))
            ->then
                ->variable($store->__destruct())->isNull()
                ->variable($store->__destruct())->isNull()
        ;
    }
}