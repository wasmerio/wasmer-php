<?php

declare(strict_types = 1);

namespace WASM;

use RuntimeException;

final class Instance
{
    private $filePath;
    private $wasmBinary;

    public function __construct(string $filePath)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException(
                "File path to WASM binary `$filePath` does not exist."
            );
        }

        $this->filePath = $filePath;
        $this->wasmBinary = wasm_read_binary($this->filePath);
        $this->wasmInstance = wasm_new_instance($this->filePath, $this->wasmBinary);
    }

    public function __call(string $name, array $arguments)
    {
        if (1 === count($arguments) && $arguments[0] instanceof ArgumentsBuilder) {
            $argumentsBuilder = $arguments[0];
        } else {
            $argumentsBuilder = new ArgumentsBuilder();

            foreach ($arguments as $i => $argument) {
                if (is_int($argument)) {
                    $argumentsBuilder->addI32($argument);
                } else {
                    throw new RuntimeException(
                        "Do not know how to handle argument #$i of `$name`."
                    );
                }
            }
        }

        $out = wasm_invoke_function($this->wasmInstance, $name, $argumentsBuilder->intoResource());

        if (false === $out) {
            throw new InvocationException(
                "Got an error when invoking `$name`."
            );
        }

        return $out;
    }
}

final class ArgumentsBuilder
{
    private $builder;

    public function __construct()
    {
        $this->builder = wasm_invoke_arguments_builder();
    }

    public function addI32(int $i32): self
    {
        wasm_invoke_arguments_builder_add_i32($this->builder, $i32);

        return $this;
    }

    public function intoResource()
    {
        return $this->builder;
    }
}

final class InvocationException extends RuntimeException {}
