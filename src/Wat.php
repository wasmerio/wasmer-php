<?php

declare(strict_types=1);

namespace Wasm;

final class Wat
{
    /**
     * @see \wat2wasm()
     */
    public static function wasm(string $wat): string
    {
        return \wat2wasm($wat);
    }
}
