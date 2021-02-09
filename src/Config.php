<?php

declare(strict_types=1);

namespace Wasm;

final class Config
{
    public const COMPILER_CRANELIFT = WASM_COMPILER_CRANELIFT;
    public const COMPILER_LLVM = WASM_COMPILER_LLVM;
    public const COMPILER_SINGLEPASS = WASM_COMPILER_SINGLEPASS;
    public const ENGINE_JIT = WASM_ENGINE_JIT;
    public const ENGINE_NATIVE = WASM_ENGINE_NATIVE;
    public const ENGINE_OBJECT_FILE = WASM_ENGINE_OBJECT_FILE;

    private static $compilers = [self::COMPILER_CRANELIFT, self::COMPILER_LLVM, self::COMPILER_SINGLEPASS];
    private static $engines = [self::ENGINE_JIT, self::ENGINE_NATIVE, self::ENGINE_OBJECT_FILE];

    /**
     * @var resource
     */
    private $inner;

    public function __construct()
    {
        $this->inner = \wasm_config_new();
    }

    public function __destruct()
    {
        try {
            \wasm_config_delete($this->inner);
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

    public function setCompiler(int $compiler): bool
    {
        if (false === in_array($compiler, self::$compilers, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return \wasm_config_set_compiler($this->inner, $compiler);
    }

    public function setEngine(int $engine): bool
    {
        if (false === in_array($engine, self::$engines, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return \wasm_config_set_engine($this->inner, $engine);
    }
}
