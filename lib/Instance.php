<?php

declare(strict_types = 1);

namespace Wasm;

use Closure;
use ReflectionObject;
use RuntimeException;

/**
 * The `Instance` class allows to compile WebAssembly bytes into a module, and
 * instantiate the module directly. Then, it is possible to call exported
 * functions with a user-friendly API.
 */
class Instance
{
    /**
     * The file path to the Wasm binary file.
     */
    protected $filePath;

    /**
     * The Wasm instance.
     */
    protected $wasmInstance;

    /**
     * Compiles and instantiates a WebAssembly binary file.
     *
     * The constructor throws a `RuntimeException` when the given file does
     * not exist, or is not readable.
     *
     * The constructor also throws a `RuntimeException` when the compilation
     * or the instantiation failed.
     *
     * # Examples
     *
     * ```php,ignore
     * $instance = new Wasm\Instance('my_program.wasm');
     * ```
     *
     * That simple.
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

        $this->wasmInstance = wasm_new_instance($wasmBytes);

        if (null === $this->wasmInstance) {
            throw new RuntimeException(
                "An error happened while compiling or instanciating the module `$filePath`:\n    " .
                str_replace("\n", "\n    ", wasm_get_last_error())
            );
        }
    }

    /**
     * Instantiates a WebAssembly module.
     *
     * This method throws a `RuntimeException` when the instantiation failed.
     */
    public static function fromModule(Module $module): self
    {
        return new class($module) extends Instance {
            public function __construct(Module $module) {
                $this->filePath = $module->getFilePath();
                $this->wasmInstance = wasm_module_new_instance($module->intoResource());

                if (null === $this->wasmInstance) {
                    throw new RuntimeException(
                        "An error happened while instanciating the module `$filePath`:\n    " .
                        str_replace("\n", "\n    ", wasm_get_last_error())
                    );
                }
            }
        };
    }

    /**
     * Calls an exported function.
     *
     * An exported function is a function that is exported by the WebAssembly
     * binary.
     *
     * The provided arguments are automatically converted to WebAssembly
     * compliant values. If arguments are missing, or if too much arguments
     * are given, an `InvocationException` exception will be thrown. If one
     * argument has a non-compliant type, an `InvocationException` exception
     * will also be thrown.
     *
     * **Reminder**: Value types are given by the following constants:
     *  * `Wasm\I32` for integer on 32 bits,
     *  * `Wasm\I64` for integer on 64 bits,
     *  * `Wasm\F32` for float on 32 bits,
     *  * `Wasm\F64` for float on 64 bits.
     *
     * # Examples
     *
     * ```php,ignore
     * $instance = new Wasm\Instance('my_program.wasm');
     * $value = $instance->sum(1, 2);
     * ```
     */
    public function __call(string $name, array $arguments)
    {
        $signature = wasm_get_function_signature($this->wasmInstance, $name);

        if (null === $signature) {
            $error = wasm_get_last_error();

            if (null === $error) {
                throw new InvocationException("Function `$name` does not exist.");
            } else {
                throw new InvocationException("Cannot invoke the function `$name` because: $error.");
            }
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

/**
 * An `InvocationException` exception is thrown when a function is invoked on
 * a Wasm instance, and failed.
 */
final class InvocationException extends RuntimeException {}
