<?php declare(strict_types=1);

namespace Wasm\Tests\Units;

use atoum\atoum;
use Wasm;

class Wat extends atoum\test
{
    public function testWat()
    {
        $this
            ->given($wat = '(module)')
            ->then
                ->string(Wasm\Wat::wasm($wat))
                    ->isNotEmpty()
            ->given($wat = '(invalid)')
            ->then
                ->exception(fn () => Wasm\Wat::wasm($wat))
                    ->isInstanceOf(Wasm\Exception\RuntimeException::class)
        ;
    }
}