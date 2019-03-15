<?php

declare(strict_types = 1);

namespace Wasm;

use RuntimeException;

/**
 * The `Module` class allows to compile WebAssembly bytes into a WebAssembly
 * module.
 */
class Module
{
    /**
     * The file path to the Wasm binary file.
     */
    private $filePath;

    /**
     * The Wasm module.
     */
    private $wasmModule;

    /**
     */
    public function __construct(string $filePath)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to Wasm binary `$filePath` does not exist.");
        }

        if (false === is_readable($filePath)) {
            throw new RuntimeException("File `$filePath` is not readable.");
        }

        $this->filePath = $filePath;
        $wasmBytes = wasm_read_bytes($this->filePath);

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

    public function instantiate(): Instance
    {
        return Instance::fromModule($this);
    }

    /**
     * Returns the file path given to the constructor.
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * Returns the inner resource representing the module.
     */
    public function intoResource()
    {
        return $this->wasmModule;
    }
}
