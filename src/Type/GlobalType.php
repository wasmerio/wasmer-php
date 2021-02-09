<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;

final class GlobalType
{
    public const MUTABILITY_VAR = WASM_VAR;
    public const MUTABILITY_CONST = WASM_CONST;

    private static $mutabilities = [self::MUTABILITY_VAR, self::MUTABILITY_CONST];

    /**
     * @var resource
     */
    private $inner;

    /**
     * @param $globaltype resource
     */
    public function __construct($globaltype)
    {
        if (false === is_resource($globaltype) && 'wasm_globaltype_t' !== get_resource_type($valtype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $globaltype;
    }

    public function __destruct()
    {
        try {
            \wasm_globaltype_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * @return resource
     */
    public function inner()
    {
        return $this->inner;
    }

    public function mutability(): int
    {
        return \wasm_globaltype_mutability($this->inner);
    }

    public function content(): ValType
    {
        return new ValType(\wasm_globaltype_content($this->inner));
    }

    public static function new(ValType $type, int $mutability): self
    {
        if (false === in_array($mutability, self::$mutabilities, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_globaltype_new($type->inner(), $mutability));
    }
}
