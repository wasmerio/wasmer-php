<?php

declare(strict_types=1);

namespace Wasm\Module;

use Wasm\Exception;
use Wasm\Store;
use Wasm\Type\FuncType;
use Wasm\Vec\Val;

/**
 * @api
 */
final class Func
{
    /**
     * @var resource The inner `wasm_func_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module\Func from a `wasm_func_t` resource.
     *
     * @param $func resource a `wasm_func_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$func` argument is not a valid `wasm_func_t` resource
     */
    public function __construct($func)
    {
        if (false === is_resource($func) || 'wasm_func_t' !== get_resource_type($func)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $func;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        if (null !== $this->inner) {
            \wasm_func_delete($this->inner);

            $this->inner = null;
        }
    }

    /**
     * @api
     */
    public function __invoke(?Val $args = null): Val
    {
        return \wasm_func_call($this->inner, $args ?? new Val());
    }

    /**
     * Return the inner func resource.
     *
     * @return resource A `wasm_func_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function asExtern(): Extern
    {
        return new Extern(\wasm_func_as_extern($this->inner));
    }

    /**
     * @api
     */
    public function type(): FuncType
    {
        return new FuncType(\wasm_func_type($this->inner));
    }

    /**
     * @api
     */
    public static function new(Store $store, FuncType $functype, callable $func): self
    {
        return new self(\wasm_func_new($store->inner(), $functype->inner(), $func));
    }
}
