<?php

declare(strict_types=1);

namespace Wasm;

/**
 * @api
 */
final class Module
{
    /**
     * @var resource The inner `wasm_config_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module from a `wasm_module_t` resource.
     *
     * @throw Exception\InvalidArgumentException If the `$module` argument is not a valid `wasm_module_t` resource
     */
    public function __construct($module)
    {
        if (false === is_resource($module) || 'wasm_module_t' !== get_resource_type($module)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $module;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        try {
            \wasm_module_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * Return the inner module resource.
     *
     * @return resource A `wasm_module_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function exports(): Vec\ExportType
    {
        return \wasm_module_exports($this->inner);
    }

    /**
     * @api
     */
    public function imports(): Vec\ImportType
    {
        return \wasm_module_imports($this->inner);
    }

    /**
     * Get or set the module's name.
     *
     * @api
     */
    public function name(?string $name = null): string
    {
        $previous = \wasm_module_name($this->inner);

        if (null === $name) {
            return $previous;
        }

        \wasm_module_set_name($this->inner, $name);

        return $previous;
    }

    /**
     * @api
     */
    public function serialize(): string
    {
        return \wasm_module_serialize($this->inner);
    }

    /**
     * @api
     */
    public static function deserialize(Store $store, string $serialized): self
    {
        return new self(\wasm_module_deserialize($store->inner(), $serialized));
    }

    /**
     * @api
     */
    public static function new(Store $store, string $wasm): self
    {
        return new self(\wasm_module_new($store->inner(), $wasm));
    }

    /**
     * @api
     */
    public static function validate(Store $store, string $wasm): bool
    {
        return \wasm_module_validate($store->inner(), $wasm);
    }
}
