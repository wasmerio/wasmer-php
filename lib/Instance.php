<?php

declare(strict_types = 1);

namespace Wasm;

use Exception;
use RuntimeException;
use WasmArrayBuffer;

/**
 * The `Instance` class allows to compile WebAssembly bytes into a module, and
 * instantiate the module directly. Then, it is possible to call exported
 * functions with a user-friendly API.
 *
 * To get a ready to use WebAssembly program, one first needs to compile the
 * bytes into a module, and second to instantiate the module. This class
 * allows to combine these two steps: The constructor compiles and
 * instantiates the module in a single-pass.
 *
 * # Examples
 *
 * ```php,ignore
 * $instance = new Wasm\Instance('my_program.wasm');
 * $result = $instance->sum(1, 2);
 * ```
 *
 * That simple.
 */
class Instance
{
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
     */
    public function __construct(string $filePath)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to Wasm binary `$filePath` does not exist.");
        }

        if (false === is_readable($filePath)) {
            throw new RuntimeException("File `$filePath` is not readable.");
        }

        $wasmBytes = wasm_fetch_bytes($filePath);
        $this->wasmInstance = wasm_new_instance($wasmBytes);

        if (null === $this->wasmInstance) {
            throw new RuntimeException(
                "An error happened while compiling or instantiating the module `$filePath`:\n    " .
                str_replace("\n", "\n    ", wasm_get_last_error())
            );
        }
    }

    /**
     * Instantiates a WebAssembly module.
     *
     * This method throws a `RuntimeException` when the instantiation failed.
     *
     * # Examples
     *
     * ```php,ignore
     * $module = new Wasm\Module('my_program.wasm');
     * $instance = Wasm\Instance::fromModule($module);
     * $result = $instance->sum(1, 2);
     * ```
     */
    public static function fromModule(Module $module): self
    {
        // Using an anonymous class allows to overwrite the constructor,
        // and to inject the `wasm_instance` resource and the file path.
        // From a type point of view, there is no difference.
        return new class($module) extends Instance {
            public function __construct(Module $module) {
                $this->wasmInstance = wasm_module_new_instance($module->intoResource());

                if (null === $this->wasmInstance) {
                    throw new RuntimeException(
                        "An error happened while instanciating the module:\n    " .
                        str_replace("\n", "\n    ", wasm_get_last_error())
                    );
                }
            }
        };
    }

    /**
     * Returns an array buffer over the instance memory if any.
     *
     * It is recommended to combine the array buffer with typed arrays
     * `Wasm\TypedArray`.
     *
     * # Examples
     *
     * ```php,ignore
     * $instance = new Wasm\Instance('my_program.wasm');
     * $memory = $instance->getMemoryBuffer();
     *
     * assert($memory instanceof WasmArrayBuffer);
     * ```
     */
    public function getMemoryBuffer(): ?WasmArrayBuffer
    {
        return wasm_get_memory_buffer($this->wasmInstance);
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
        try {
            $result = wasm_invoke_function(
                $this->wasmInstance,
                $name,
                $arguments
            );
        } catch (Exception $e) {
            $message = $e->getMessage();

            throw new InvocationException("Got an error when invoking `$name`: $message", 0, $e);
        }

        return $result;
    }
}

/**
 * An `InvocationException` exception is thrown when a function is invoked on
 * a Wasm instance, and failed.
 */
final class InvocationException extends RuntimeException {}
