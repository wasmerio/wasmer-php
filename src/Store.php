<?php

declare(strict_types=1);

namespace Wasm;

final class Store
{
    /**
     * @var resource
     */
    private $inner;

    public function __construct(Engine $engine)
    {
        $this->inner = \wasm_store_new($engine->inner());
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
}
