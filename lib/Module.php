<?php

declare(strict_types = 1);

namespace Wasm;

use RuntimeException;

/**
 * The `Module` class allows to compile WebAssembly bytes into a WebAssembly
 * module.
 *
 * To get a ready to use WebAssembly program, one first needs to compile the
 * bytes into a module, and second to instantiate the module. This class
 * addresses the first step. To instantiate the module, call the `instantiate`
 * method.
 *
 * # Examples
 *
 * ```php,ignore
 * $module = new Wasm\Module('my_program.wasm');
 * $instance = $module->instantiate();
 * $result = $instance->sum(1, 2);
 * ```
 */
class Module
{
    /**
     * The Wasm module.
     */
    private $wasmModule;

    /**
     * Compiles WebAssembly bytes from a file into a module.
     *
     * The constructor throws a `RuntimeException` when the given file does
     * not exist, or is not readable.
     *
     * The constructor also throws a `RuntimeException` when the compilation
     * failed.
     */
    public function __construct(string $filePath)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to Wasm binary `$filePath` does not exist.");
        }

        if (false === is_readable($filePath)) {
            throw new RuntimeException("File `$filePath` is not readable.");
        }

        $wasmBytes = wasm_read_bytes($filePath);

        if (null === $wasmBytes) {
            throw new RuntimeException("An error happened while reading the module `$filePath`.");
        }

        if (false === wasm_validate($wasmBytes)) {
            throw new RuntimeException("Bytes in `$filePath` are invalid.");
        }

        $this->wasmModule = wasm_compile($wasmBytes);

        if (null === $this->wasmModule) {
            throw new RuntimeException(
                "An error happened while compiling the module `$filePath`:\n    " .
                str_replace("\n", "\n    ", wasm_get_last_error())
            );
        }
    }

    /**
     * Instantiates the module.
     *
     * # Examples
     *
     * The following code:
     *
     * ```php,ignore
     * $module = new Wasm\Module('my_program.wasm');
     * $instance = $module->instantiate();
     * ```
     *
     * is a shortcut to:
     *
     * ```php,ignore
     * $module = new Wasm\Module('my_program.wasm');
     * $instance = Wasm\Instance::fromModule($module);
     * ```
     */
    public function instantiate(): Instance
    {
        return Instance::fromModule($this);
    }

    /**
     * Returns the inner resource representing the module.
     */
    public function intoResource()
    {
        return $this->wasmModule;
    }
}
