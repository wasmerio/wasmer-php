<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Store extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->given($engine = new Wasm\Engine())
            ->then
                ->object(new Wasm\Store($engine))
        ;
    }

    public function testDestruct()
    {
        $this
            ->given($store = new Wasm\Store(new Wasm\Engine()))
            ->then
                ->variable($store->__destruct())->isNull()
                ->variable($store->__destruct())->isNull()
        ;
    }
}