<?php

namespace WASM\Tests\Units;

use WASM\Tests\Suite;

class Instance extends Suite
{
    public function test_foo()
    {
        $this->integer(1)->isEqualTo(1);
    }
}
