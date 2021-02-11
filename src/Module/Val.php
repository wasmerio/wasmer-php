<?php

declare(strict_types=1);

namespace Wasm\Module;

use Wasm\Exception;

/**
 * @api
 */
final class Val
{
    /**
     * @var resource The inner `wasm_val_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Module\Val from a `wasm_val_t` resource.
     *
     * @param $val resource a `wasm_val_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$val` argument is not a valid `wasm_val_t` resource
     */
    public function __construct($val)
    {
        if (false === is_resource($val) || 'wasm_val_t' !== get_resource_type($val)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $val;
    }

    /**
     * @ignore
     */
    public function __destruct()
    {
        if (null !== $this->inner) {
            \wasm_val_delete($this->inner);

            $this->inner = null;
        }
    }

    /**
     * Return the inner val resource.
     *
     * @return resource A `wasm_val_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * @api
     */
    public function kind(): int
    {
        return \wasm_val_kind($this->inner);
    }

    /**
     * @api
     */
    public function value(): int | float
    {
        return \wasm_val_value($this->inner);
    }

    /**
     * @api
     */
    public static function new(mixed $val): self
    {
        if (is_resource($val) && 'wasm_val_t' === get_resource_type($val)) {
            $val = \wasm_val_value($val);
        }

        if (is_int($val)) {
            try {
                return self::newI32($val);
            } catch (Exception\InvalidArgumentException) {
                return self::newI64($val);
            }
        }

        if (is_float($val)) {
            try {
                return self::newF32($val);
            } catch (Exception\InvalidArgumentException) {
                return self::newF64($val);
            }
        }

        throw new Exception\InvalidArgumentException();
    }

    /**
     * @api
     */
    public static function newI32(int $val): self
    {
        if ($val < -0x7FFFFFFF || $val > 0x7FFFFFFF) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_val_i32($val));
    }

    /**
     * @api
     */
    public static function newI64(int $val): self
    {
        return new self(\wasm_val_i64($val));
    }

    /**
     * @api
     */
    public static function newF32(float $val): self
    {
        if ($val < -3.40282347e+38 || $val > 3.40282347e+38) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_val_f32($val));
    }

    /**
     * @api
     */
    public static function newF64(float $val): self
    {
        return new self(\wasm_val_f64($val));
    }
}
