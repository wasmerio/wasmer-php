<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;

/**
 * @todo Fix doc & example
 *
 * Function types classify the signature of functions, mapping a vector of parameters to a vector of results. They are
 * also used to classify the inputs and outputs of instructions.
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
 * @see https://webassembly.github.io/spec/core/syntax/types.html#function-types WebAssembly Specification - Function Types
 */
final class ExternType
{
    public const KIND_FUNC = WASM_EXTERN_FUNC;
    public const KIND_GLOBAL = WASM_EXTERN_GLOBAL;
    public const KIND_TABLE = WASM_EXTERN_TABLE;
    public const KIND_MEMORY = WASM_EXTERN_MEMORY;

    private static $kinds = [self::KIND_FUNC, self::KIND_GLOBAL, self::KIND_TABLE, self::KIND_MEMORY];

    /**
     * @var resource The inner `wasm_externtype_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Type\ExternType from a `wasm_externtype_t` resource.
     *
     * @param $functype resource a `wasm_externtype_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$externtype` argument is not a valid `wasm_externtype_t` resource
     */
    public function __construct($externtype)
    {
        if (false === is_resource($externtype) || 'wasm_externtype_t' !== get_resource_type($externtype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $externtype;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        try {
            \wasm_externtype_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * Return the inner extern type resource.
     *
     * @return resource A `wasm_externtype_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function asGlobalType(): GlobalType
    {
        return new GlobalType(\wasm_externtype_as_globaltype($this->inner));
    }

    /**
     * @api
     */
    public function asFuncType(): FuncType
    {
        return new FuncType(\wasm_externtype_as_functype($this->inner));
    }

    /**
     * @api
     */
    public function kind(): int
    {
        return \wasm_externtype_kind($this->inner);
    }
}
