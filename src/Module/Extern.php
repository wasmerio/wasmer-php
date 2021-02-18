<?php

declare(strict_types=1);

namespace Wasm\Module;

use Wasm\Exception;
use Wasm\Type\ExternType;

/**
 * @api
 */
final class Extern
{
    /**
     * @var resource The inner `wasm_extern_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module\Extern from a `wasm_extern_t` resource.
     *
     * @param $extern resource a `wasm_extern_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$extern` argument is not a valid `wasm_extern_t` resource
     */
    public function __construct($extern)
    {
        if (false === is_resource($extern) || 'wasm_extern_t' !== get_resource_type($extern)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $extern;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        if (null !== $this->inner) {
            \wasm_extern_delete($this->inner);

            $this->inner = null;
        }
    }

    /**
     * Return the inner extern resource.
     *
     * @return resource A `wasm_extern_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function asFunc(): Func
    {
        return new Func(\wasm_extern_as_func($this->inner));
    }

    /**
     * @api
     */
    public function asGlobal(): Globl
    {
        return new Globl(\wasm_extern_as_global($this->inner));
    }

    /**
     * @api
     */
    public function kind(): int
    {
        return \wasm_extern_kind($this->inner);
    }

    /**
     * @api
     */
    public function type(): ExternType
    {
        return new ExternType(\wasm_extern_type($this->inner));
    }
}
