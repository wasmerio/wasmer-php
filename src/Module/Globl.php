<?php

declare(strict_types=1);

namespace Wasm\Module;

use Wasm\Exception;
use Wasm\Store;
use Wasm\Type\GlobalType;
use Wasm\Type\ValType;

/**
 * @api
 */
final class Globl
{
    /**
     * @var resource The inner `wasm_global_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module\Globl from a `wasm_global_t` resource.
     *
     * @param $global resource a `wasm_global_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$global` argument is not a valid `wasm_global_t` resource
     */
    public function __construct($global)
    {
        if (false === is_resource($global) || 'wasm_global_t' !== get_resource_type($global)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $global;
    }

    /**
     * @ignore
     */
    public function __clone(): void
    {
        $this->inner = \wasm_global_copy($this->inner);
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        try {
            \wasm_global_delete($this->inner);
        } catch (\TypeError $error) {
            if (is_resource($this->inner)) {
                throw $error;
            }
        }
    }

    /**
     * Return the inner global resource.
     *
     * @return resource A `wasm_global_t` resource
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
        return new Extern(\wasm_global_as_extern($this->inner));
    }

    /**
     * @api
     */
    public function get(): Val
    {
        return new Val(\wasm_global_get($this->inner));
    }

    /**
     * @api
     */
    public function same(self $other): bool
    {
        return \wasm_global_same($this->inner, $other->inner());
    }

    /**
     * @api
     */
    public function set(int | float | Val $value): void
    {
        if (!$value instanceof Val) {
            $globaltype = $this->type();
            $valtype = $globaltype->content();

            switch ($valtype->kind()) {
                case ValType::KIND_I32:
                    $value = Val::newI32($value);
                    break;

                case ValType::KIND_I64:
                    $value = Val::newI64($value);
                    break;

                case ValType::KIND_F32:
                    $value = Val::newF32((float) $value);
                    break;

                case ValType::KIND_F64:
                    $value = Val::newF64((float) $value);
                    break;

                default:
                    throw new Exception\InvalidArgumentException();
            }
        }

        \wasm_global_set($this->inner, $value->inner());
    }

    /**
     * @api
     */
    public function type(): GlobalType
    {
        return new GlobalType(\wasm_global_type($this->inner));
    }

    /**
     * @api
     */
    public static function new(Store $store, GlobalType $globaltype, int | float | Val $val): self
    {
        if ($val instanceof Val) {
            return new self(\wasm_global_new($store->inner(), $globaltype->inner(), $val->inner()));
        }

        $val = Val::new($val);

        return new self(\wasm_global_new($store->inner(), $globaltype->inner(), $val->inner()));
    }
}
