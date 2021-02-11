<?php

declare(strict_types=1);

namespace Wasm\Module;

use Wasm\Exception;
use Wasm\Module;
use Wasm\Store;
use Wasm\Vec;

/**
 * @api
 */
final class Instance
{
    /**
     * @var resource The inner `wasm_instance_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module\Extern from a `wasm_instance_t` resource.
     *
     * @param $instance resource a `wasm_instance_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$instance` argument is not a valid `wasm_instance_t` resource
     */
    public function __construct($instance)
    {
        if (false === is_resource($instance) || 'wasm_instance_t' !== get_resource_type($instance)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $instance;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        if (null !== $this->inner) {
            \wasm_instance_delete($this->inner);

            $this->inner = null;
        }
    }

    /**
     * Return the inner extern resource.
     *
     * @return resource A `wasm_instance_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function exports(): Vec\Extern
    {
        return \wasm_instance_exports($this->inner);
    }

    /**
     * @api
     */
    public static function new(Store $store, Module $module, Vec\Extern $externs): self
    {
        return new self(\wasm_instance_new($store->inner(), $module->inner(), $externs));
    }
}
