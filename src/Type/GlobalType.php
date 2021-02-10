<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;

/**
 * Global types classify global variables, which hold a value and can either be mutable or immutable.
 *
 * ```php
 * <?php declare(strict_types=1);
 *
 * use Wasm\Type\{GlobalType, ValType};
 *
 * $valtype = Type\ValType::new(Type\ValType::KIND_I32);
 * $globaltype = Type\GlobalType::new($valtype, Type\GlobalType::MUTABILITY_VAR);
 * ```
 *
 * @api
 *
 * @see https://webassembly.github.io/spec/core/syntax/types.html#global-types WebAssembly Specification - Global Types
 */
final class GlobalType
{
    public const MUTABILITY_VAR = WASM_VAR;
    public const MUTABILITY_CONST = WASM_CONST;

    private static array $mutabilities = [self::MUTABILITY_VAR, self::MUTABILITY_CONST];

    /**
     * @var resource The inner `wasm_globaltype_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Type\GlobalType from a `wasm_globaltype_t` resource.
     *
     * @param $globaltype resource a `wasm_globaltype_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$globaltype` argument is not a valid `wasm_globaltype_t` resource
     */
    public function __construct($globaltype)
    {
        if (false === is_resource($globaltype) || 'wasm_globaltype_t' !== get_resource_type($globaltype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $globaltype;
    }

    /**
     * @ignore
     */
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
     * Return the inner global type resource.
     *
     * @return resource A `wasm_globaltype_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function asExternType(): ExternType
    {
        return new ExternType(\wasm_globaltype_as_externtype($this->inner));
    }

    /**
     * Return the global type's mutability.
     *
     * @api
     */
    public function mutability(): int
    {
        return \wasm_globaltype_mutability($this->inner);
    }

    /**
     * @api
     */
    public function content(): ValType
    {
        return new ValType(\wasm_globaltype_content($this->inner));
    }

    /**
     * @api
     *
     * @throw Exception\InvalidArgumentException If the `$mutability` is not a valid mutability
     */
    public static function new(ValType $type, int $mutability): self
    {
        if (false === in_array($mutability, self::$mutabilities, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_globaltype_new($type->inner(), $mutability));
    }
}
