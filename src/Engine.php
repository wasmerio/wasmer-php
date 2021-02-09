<?php

declare(strict_types=1);

namespace Wasm;

final class Engine
{
    /**
     * @var resource
     */
    private $inner;

    public function __construct(?Config $config = null)
    {
        $this->inner = null === $config ? \wasm_engine_new() : \wasm_engine_new_with_config($config->inner());
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
}
