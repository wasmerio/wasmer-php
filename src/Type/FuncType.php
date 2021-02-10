<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;
use Wasm\Vec;

/**
 * Function types classify the signature of functions, mapping a vector of parameters to a vector of results. They are
 * also used to classify the inputs and outputs of instructions.
 *
 * @todo Fix example
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
final class FuncType
{
    /**
     * @var resource The inner `wasm_functype_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Type\FuncType from a `wasm_functype_t` resource.
     *
     * @param $functype resource a `wasm_functype_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$functype` argument is not a valid `wasm_functype_t` resource
     */
    public function __construct($functype)
    {
        if (false === is_resource($functype) || 'wasm_functype_t' !== get_resource_type($functype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $functype;
    }

    /**
     * @ignore
     */
    public function __clone(): void
    {
        $this->inner = \wasm_functype_copy($this->inner);
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        try {
            \wasm_functype_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * Return the inner function type resource.
     *
     * @return resource A `wasm_functype_t` resource
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
        return new ExternType(\wasm_functype_as_externtype($this->inner));
    }

    /**
     * @api
     */
    public function params(): Vec\ValType
    {
        return \wasm_functype_params($this->inner);
    }

    /**
     * @api
     */
    public function results(): Vec\ValType
    {
        return \wasm_functype_results($this->inner);
    }

    /**
     * @api
     */
    public static function new(Vec\ValType $params, Vec\ValType $results): self
    {
        return new self(\wasm_functype_new($params, $results));
    }
}
