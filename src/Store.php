<?php

declare(strict_types=1);

namespace Wasm;

/**
 * @api
 */
final class Store
{
    /**
     * Create a Wasm\Store from a `wasm_store_t` resource.
     *
     * @param $store resource a `wasm_store_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$store` argument is not a valid `wasm_store_t` resource
     */
    public function __construct($store)
    {
        if (false === is_resource($store) || 'wasm_store_t' !== get_resource_type($store)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $store;
    }

    public function __destruct()
    {
        try {
            \wasm_store_delete($this->inner);
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

    /**
     * @api
     */
    public static function new(Engine $engine): self
    {
        return new self(\wasm_store_new($engine->inner()));
    }
}
