<?php

declare(strict_types=1);

namespace Wasm\Type;

use Wasm\Exception;

final class ValType
{
    public const KIND_I32 = WASM_I32;
    public const KIND_I64 = WASM_I64;
    public const KIND_F32 = WASM_F32;
    public const KIND_F64 = WASM_F64;
    public const KIND_ANYREF = WASM_ANYREF;
    public const KIND_FUNCREF = WASM_FUNCREF;

    private static $kinds = [self::KIND_I32, self::KIND_I64, self::KIND_F32, self::KIND_F64, self::KIND_ANYREF, self::KIND_FUNCREF];

    /**
     * @var resource
     */
    private $inner;

    /**
     * @param $valtype resource
     */
    public function __construct($valtype)
    {
        if (false === is_resource($valtype) && 'wasm_valtype_t' !== get_resource_type($valtype)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $valtype;
    }

    public function __destruct()
    {
        try {
            \wasm_valtype_delete($this->inner);
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

    public function isNum(): bool
    {
        return \wasm_valtype_is_num($this->inner);
    }

    public function isRef(): bool
    {
        return \wasm_valtype_is_ref($this->inner);
    }

    public function kind(): int
    {
        return \wasm_valtype_kind($this->inner);
    }

    public static function new(int $kind): self
    {
        if (false === in_array($kind, self::$kinds, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return new self(\wasm_valtype_new($kind));
    }
}
