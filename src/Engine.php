<?php

declare(strict_types=1);

namespace Wasm;

/**
 * @api
 */
final class Engine
{
    /**
     * @var resource
     */
    private $inner;

    /**
     * @param ?resource $engine
     */
    public function __construct($engine = null)
    {
        $engine = $engine ?? \wasm_engine_new();

        if (false === is_resource($engine) || 'wasm_engine_t' !== get_resource_type($engine)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $engine;
    }

    public function __destruct()
    {
        try {
            \wasm_engine_delete($this->inner);
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
     *
     * @throw Exception\InvalidArgumentException If the `$kind` is not a valid value kind
     */
    public static function new(?Config $config = null): self
    {
        return new self($config ? \wasm_engine_new_with_config($config->inner()) : \wasm_engine_new());
    }
}
