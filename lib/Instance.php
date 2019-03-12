<?php

declare(strict_types = 1);

namespace Wasm;

use Closure;
use ReflectionObject;
use RuntimeException;

final class Instance
{
    private $filePath;
    private $wasmRuntime;
    private $importedFunctions = [];

    public function __construct(string $filePath)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to WASM binary `$filePath` does not exist.");
        }

        $this->filePath = $filePath;
        $wasmBytes = wasm_read_bytes($this->filePath);

        $this->wasmInstance = wasm_new_instance($wasmBytes);

        if (null === $this->wasmInstance) {
            throw new RuntimeException("An error happened while instanciating the module `$filePath`.");
        }
    }

    public function __call(string $name, array $arguments)
    {
        $signature = wasm_get_function_signature($this->wasmInstance, $name);

        if (null === $signature) {
            throw new InvocationException("Function `$name` does not exist.");
        }

        $number_of_expected_arguments = count($signature) - 1;
        $number_of_given_arguments = count($arguments);
        $diff = $number_of_expected_arguments - $number_of_given_arguments;

        if ($diff > 0) {
            throw new InvocationException(
                "Missing $diff argument(s) when calling `$name`: " .
                "Expect $number_of_expected_arguments arguments, " .
                "given $number_of_given_arguments."
            );
        } elseif ($diff < 0) {
            $diff = abs($diff);

            throw new InvocationException(
                "Given $diff extra argument(s) when calling `$name`: " .
                "Expect $number_of_expected_arguments arguments, " .
                "given $number_of_given_arguments."
            );
        }

        $wasmArguments = [];

        foreach ($arguments as $i => $argument) {
            $s = $i + 1;

            switch ($signature[$i]) {
                case I32:
                    if (!is_int($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `i32` (integer).");
                    }

                    $wasmArguments[] = wasm_value(I32, $argument);

                    break;

                case I64:
                    if (!is_int($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `i64` (integer).");
                    }

                    $wasmArguments[] = wasm_value(I64, $argument);

                    break;

                case F32:
                    if (!is_float($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `f32` (float).");
                    }

                    $wasmArguments[] = wasm_value(F32, $argument);

                    break;

                case F64:
                    if (!is_float($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `f64` (float).");
                    }

                    $wasmArguments[] = wasm_value(F64, $argument);

                    break;

                default:
                    throw new InvocationException("Unknown argument type `$signature[$i]` at position #$s of `$name`.");
            }
        }

        $result = wasm_invoke_function(
            $this->wasmInstance,
            $name,
            $wasmArguments
        );

        if (false === $result) {
            throw new InvocationException("Got an error when invoking `$name`.");
        }

        return $result;
    }
}

final class InvocationException extends RuntimeException {}
