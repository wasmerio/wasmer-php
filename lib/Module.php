<?php

declare(strict_types = 1);

namespace Wasm;

use RuntimeException;
use Serializable;

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
 * To compile the bytes in `my_program.wasm` to a WebAssembly module:
 *
 * ```php,ignore
 * $module = new Wasm\Module('my_program.wasm');
 * $instance = $module->instantiate();
 * $result = $instance->sum(1, 2);
 * ```
 *
 * To compile the module only once per multiple PHP requests, one will write:
 *
 * ```php,ignore
 * $module = new Wasm\Module('my_program.wasm', Wasm\Module::PERSISTENT);
 * $instance = $module->instantiate();
 * $result = $instance->sum(1, 2);
 * ```
 */
class Module implements Serializable
{
    /**
     * Instructs that the underlying module resource must be persistent.
     */
    const PERSISTENT = true;

    /**
     * Instructs that the underlying module resource must **not** be
     * persistent.
     */
    const VOLATILE = false;

    /**
     * The Wasm module.
     */
    private $wasmModule;

    /**
     * Compiles WebAssembly bytes from a file into a module.
     *
     * The `$persistence` flag allows to compile the module only once per
     * multiple PHP request executions: It can dramatically improves the set
     * up of your WebAssembly program when it is quite large.
     *
     * When the `$persistence` flag is turned to `self::PERSISTENT`, the given
     * file will be read only once and the module will be compiled only
     * once. The underlying `wasm_module` resource will be registered as
     * persistent across PHP requests. As a side-effect, if the file content
     * changes, the module will not be re-compiled.
     *
     * To force to compile a new fresh module, the `$persistence` flag must be
     * turned to `self::VOLATILE`, which is the default value. See also the
     * `wasm_module_clean_up_persistent_resources` function.
     *
     * The constructor throws a `RuntimeException` when the given file does
     * not exist, or is not readable.
     *
     * The constructor also throws a `RuntimeException` when the compilation
     * failed.
     */
    public function __construct(string $filePath, bool $persistence = self::VOLATILE)
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to Wasm binary `$filePath` does not exist.");
        }

        if (false === is_readable($filePath)) {
            throw new RuntimeException("File `$filePath` is not readable.");
        }

        $wasmBytes = wasm_fetch_bytes($filePath);
        $wasmModuleUniqueIdentifier = null;

        if (self::PERSISTENT === $persistence) {
            $wasmModuleUniqueIdentifier = $this->getUniqueIdentifier($filePath);
        }

        $this->wasmModule = wasm_compile($wasmBytes, $wasmModuleUniqueIdentifier);

        if (null === $this->wasmModule) {
            throw new RuntimeException(
                "An error happened while compiling the module `$filePath`:\n    " .
                str_replace("\n", "\n    ", wasm_get_last_error())
            );
        }
    }

    /**
     * Generates a unique identifier for this module.
     *
     * This is used when the module needs to be persistent: It identifies the
     * module resource by a unique string.
     */
    protected function getUniqueIdentifier(string $filePath): string
    {
        $out = realpath($filePath);

        if (true === function_exists('hash')) {
            $out = hash('sha3-512', $out);
        }

        return $out;
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

    /**
     * Serializes this module with the standard `serialize` PHP function.
     *
     * # Examples
     *
     * ```php,ignore
     * $module = new Wasm\Module('my_program.wasm');
     * $serialized_module = serialize($module);
     * ```
     */
    public function serialize(): string
    {
        $serializedModule = wasm_module_serialize($this->intoResource());

        if (null === $serializedModule) {
            throw new RuntimeException('Failed to serialize the module.');
        }

        return $serializedModule;
    }

    /**
     * Deserializes a (supposedly) serialized module, with the standard
     * `unserialize` PHP function.
     *
     * This method throws a `RuntimeException` if the deserialization failed.
     *
     * # Examples
     *
     * ```php,ignore
     * $module = new Wasm\Module('my_program.wasm');
     * $serialized_module = serialize($module);
     * unset($module);
     *
     * $module = unserialize($serialized_module, [Wasm\Module::class]);
     * $instance = $module->instantiate();
     * $result = $instance->sum(1, 2);
     * ```
     */
    public function unserialize($serializedModule)
    {
        $module = wasm_module_deserialize($serializedModule);

        if (null === $module) {
            throw new RuntimeException('Failed to deserialize the module.');
        }

        $this->wasmModule = $module;
    }
}
