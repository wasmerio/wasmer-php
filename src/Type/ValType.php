<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;

/**
 * Value types classify the individual values that WebAssembly code can compute with and the values that a variable accepts.
 *
 * ```php
 * <?php declare(strict_types=1);
 *
 * use Wasm\Type\ValType;
 *
 * $valtype = Type\ValType::new(Type\ValType::KIND_I32);
 * ```
 *
 * @api
 *
 * @see https://webassembly.github.io/spec/core/syntax/types.html#value-types WebAssembly Specification - Value Types
 */
final class ValType
{
    public const KIND_I32 = WASM_I32;
    public const KIND_I64 = WASM_I64;
    public const KIND_F32 = WASM_F32;
    public const KIND_F64 = WASM_F64;
    public const KIND_ANYREF = WASM_ANYREF;
    public const KIND_FUNCREF = WASM_FUNCREF;

    private static $kinds = [self::KIND_I32, self::KIND_I64, self::KIND_F32, self::KIND_F64, self::KIND_ANYREF, self::KIND_FUNCREF];

    /**
     * @var resource The inner `wasm_valtype_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Type\ValType from a `wasm_valtype_t` resource.
     *
     * @param $valtype resource a `wasm_valtype_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$valtype` argument is not a valid `wasm_valtype_t` resource
     */
    public function __construct($valtype)
    {
        if (false === is_resource($valtype) || 'wasm_valtype_t' !== get_resource_type($valtype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $valtype;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        try {
            \wasm_valtype_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * Return the inner value type resource.
     *
     * @return resource A `wasm_valtype_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * Check if the value type is a numeric type.
     *
     * @api
     */
    public function isNum(): bool
    {
        return \wasm_valtype_is_num($this->inner);
    }

    /**
     * Check if the value type is a reference type.
     *
     * @api
     */
    public function isRef(): bool
    {
        return \wasm_valtype_is_ref($this->inner);
    }

    /**
     * Check the value type's type.
     *
     * @api
     */
    public function kind(): int
    {
        return \wasm_valtype_kind($this->inner);
    }

    /**
     * @api
     *
     * @throw Exception\InvalidArgumentException If the `$kind` is not a valid value kind
     */
    public static function new(int $kind): self
    {
        if (false === in_array($kind, self::$kinds, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_valtype_new($kind));
    }
}
