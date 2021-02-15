<?php

declare(strict_types=1);

namespace Wasm\Tests;

use PHPUnit\Framework\TestCase;
use Wasm;
use Wasm\Exception;

/**
 * @test
 * @small
 */
final class Wat extends TestCase
{
    /**
     * @test
     */
    public function wat(): void
    {
        self::assertNotEmpty(Wasm\Wat::wasm('(module)'));

        try {
            Wasm\Wat::wasm('(invalid)');
        } catch (Exception\RuntimeException) {
        }
    }
}
