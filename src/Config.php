<?php

declare(strict_types=1);

namespace Wasm;

/**
 * @api
 */
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
     * @var resource The inner `wasm_config_t` resource
     */
    private $inner;

    /**
     * Create a Wasm\Config from a `wasm_config_t` resource.
     *
     * @param $config ?resource a `wasm_config_t` resource
     *
     * @throw Exception\InvalidArgumentException If the `$config` argument is not a valid `wasm_config_t` resource
     */
    public function __construct($config = null)
    {
        $config = $config ?? \wasm_config_new();

        if (false === is_resource($config) || 'wasm_config_t' !== get_resource_type($config)) {
            throw new Exception\InvalidArgumentException();
        }

        $this->inner = $config;
    }

    /**
     * @ignore
     */
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
     * Return the inner config resource.
     *
     * @return resource A `wasm_config_t` resource
     */
    public function inner()
    {
        return $this->inner;
    }

    /**
     * Set the compiler for the current configuration.
     *
     * @throw Exception\InvalidArgumentException If the `$compiler` is not a valid compiler
     */
    public function setCompiler(int $compiler): bool
    {
        if (false === in_array($compiler, self::$compilers, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return \wasm_config_set_compiler($this->inner, $compiler);
    }

    /**
     * Set the engine for the current configuration.
     *
     * @throw Exception\InvalidArgumentException If the `$engine` is not a valid compiler
     */
    public function setEngine(int $engine): bool
    {
        if (false === in_array($engine, self::$engines, true)) {
            throw new Exception\InvalidArgumentException();
        }

        return \wasm_config_set_engine($this->inner, $engine);
    }

    /**
     * @api
     *
     * @throw Exception\InvalidArgumentException If the `$compiler` or `$engine` arguments are not valid, respectively,compilers and engines.
     */
    public static function new(?int $compiler = null, ?int $engine = null): self
    {
        $config = new self(\wasm_config_new());

        if (null !== $compiler) {
            $config->setCompiler($compiler);
        }

        if (null !== $engine) {
            $config->setEngine($engine);
        }

        return $config;
    }
}
