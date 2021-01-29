<?php

/** @generate-function-entries */

namespace Wasm\Vec {
    ///////////////////////////////////////////////////////////////////////////////
    // Type Representations

    final class ExportType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrExporttypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class ExternType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrExterntypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class FuncType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrFunctypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class GlobalType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrGlobaltypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class ImportType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrImporttypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class MemoryType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrMemorytypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class TableType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrTabletypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class ValType implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrValtypes = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Objects

    final class Extern implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrExterns = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class Frame implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrFrames = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }

    final class Val implements \Countable, \ArrayAccess {
        public function __construct(array|int|null $sizeOrVals = null) {}
        public function count(): int {}
        public function offsetExists(mixed $offset): bool {}
        /** @return resource */
        public function offsetGet(mixed $offset): mixed {}
        /** @param resource $value */
        public function offsetSet(mixed $offset, mixed $value): void {}
        /** @throw \Exception */
        public function offsetUnset(mixed $offset): void {}
    }
}

namespace {

    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Environment

    // Configuration

    /** @return resource */
    function wasm_config_new() {}
    /** @param resource $config */
    function wasm_config_delete($config): bool {}
    /** @param resource $config */
    function wasm_config_set_compiler($config, int $compiler): bool {}
    /** @param resource $config */
    function wasm_config_set_engine($config, int $engine): bool {}

    // Engine

    /** @return resource */
    function wasm_engine_new() {}
    /**
     * @param resource $config
     *
     * @return resource
     */
    function wasm_engine_new_with_config($config) {}
    /** @param resource $engine */
    function wasm_engine_delete($engine): bool {}


    // Store

    /**
     * @param resource $engine
     *
     * @return resource
     */
    function wasm_store_new($engine) {}
    /** @param resource $store */
    function wasm_store_delete($store): bool {}


    ///////////////////////////////////////////////////////////////////////////////
    // Type Representations

    // Value Types

    /** @return resource */
    function wasm_valtype_new(int $kind) {}
    /** @param resource $valtype */
    function wasm_valtype_delete($valtype): bool {}
    /** @param resource $valtype */
    function wasm_valtype_kind($valtype): int {}
    /** @param resource $valtype */
    function wasm_valtype_is_num($valtype): bool {}
    /** @param resource $valtype */
    function wasm_valtype_is_ref($valtype): bool {}
    /**
     * @param resource $valtype
     *
     * @return resource
     */
    function wasm_valtype_copy($valtype) {}

    function wasm_valkind_is_num(int $kind): bool {}
    function wasm_valkind_is_ref(int $kind): bool {}


    // Function Types

    /** @return resource */
    function wasm_functype_new(Wasm\Vec\ValType $params, Wasm\Vec\ValType $results) {}
    /** @param resource $functype */
    function wasm_functype_delete($functype): bool {}
    /** @param resource $functype */
    function wasm_functype_params($functype): Wasm\Vec\ValType {}
    /** @param resource $functype */
    function wasm_functype_results($functype): Wasm\Vec\ValType {}
    /**
     * @param resource $functype
     *
     * @return resource
     */
    function wasm_functype_copy($functype) {}
    /**
     * @param resource $functype
     *
     * @return resource
     */
    function wasm_functype_as_externtype($functype) {}


    // Global Types

    /**
     * @param resource $valtype
     *
     * @return resource
     */
    function wasm_globaltype_new($valtype, int $mutability) {}
    /** @param resource $globaltype */
    function wasm_globaltype_delete($globaltype): bool {}
    /**
     * @param resource $globaltype
     *
     * @return resource
     */
    function wasm_globaltype_content($globaltype) {}
    /** @param resource $globaltype */
    function wasm_globaltype_mutability($globaltype): int {}
    /**
     * @param resource $globaltype
     *
     * @return resource
     */
    function wasm_globaltype_copy($globaltype) {}
    /**
     * @param resource $globaltype
     *
     * @return resource
     */
    function wasm_globaltype_as_externtype($globaltype) {}


    // Limits

    /** @return resource */
    function wasm_limits_new(int $min, int $max) {}
    /** @param resource $limits */
    function wasm_limits_min($limits): int {}
    /** @param resource $limits */
    function wasm_limits_max($limits): int {}


    // Table Types

    /**
     * @param resource $valtype
     * @param resource $limits
     *
     * @return resource
     */
    function wasm_tabletype_new($valtype, $limits) {}
    /** @param resource $tabletype */
    function wasm_tabletype_delete($tabletype): bool {}
    /**
     * @param resource $tabletype
     *
     * @return resource
     */
    function wasm_tabletype_element($tabletype) {}
    /**
     * @param resource $tabletype
     *
     * @return resource
     */
    function wasm_tabletype_limits($tabletype) {}
    /**
     * @param resource $tabletype
     *
     * @return resource
     */
    function wasm_tabletype_copy($tabletype) {}
    /**
     * @param resource $tabletype
     *
     * @return resource
     */
    function wasm_tabletype_as_externtype($tabletype) {}


    // Memory Types

    /**
     * @param resource $limits
     *
     * @return resource
     */
    function wasm_memorytype_new($limits) {}
    /** @param resource $memorytype */
    function wasm_memorytype_delete($memorytype): bool {}
    /**
     * @param resource $memorytype
     *
     * @return resource
     */
    function wasm_memorytype_limits($memorytype) {}
    /**
     * @param resource $memorytype
     *
     * @return resource
     */
    function wasm_memorytype_copy($memorytype) {}
    /**
     * @param resource $memorytype
     *
     * @return resource
     */
    function wasm_memorytype_as_externtype($memorytype) {}


    // Extern Types

    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_externtype_kind($externtype) {}
    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_externtype_as_functype($externtype) {}
    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_externtype_as_globaltype($externtype) {}
    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_externtype_as_tabletype($externtype) {}
    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_externtype_as_memorytype($externtype) {}


    // Import Types

    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_importtype_new(string $module, string $name, $externtype) {}
    /** @param resource $importype */
    function wasm_importtype_delete($importype): bool {}
    /** @param resource $importype */
    function wasm_importtype_module($importype): string {}
    /** @param resource $importype */
    function wasm_importtype_name($importype): string {}
    /**
     * @param resource $importype
     *
     * @return resource
     */
    function wasm_importtype_type($importype) {}
    /**
     * @param resource $importype
     *
     * @return resource
     */
    function wasm_importtype_copy($importype) {}


    // Export Types

    /**
     * @param resource $externtype
     *
     * @return resource
     */
    function wasm_exporttype_new(string $name, $externtype) {}
    /** @param resource $exportype */
    function wasm_exporttype_delete($exportype): bool {}
    /** @param resource $exportype */
    function wasm_exporttype_name($exportype): string {}
    /**
     * @param resource $exportype
     *
     * @return resource
     */
    function wasm_exporttype_type($exportype) {}
    /**
     * @param resource $exportype
     *
     * @return resource
     */
    function wasm_exporttype_copy($exportype) {}


    ///////////////////////////////////////////////////////////////////////////////
    // Runtime Objects

    //Values

    /** @param resource $val */
    function wasm_val_delete($val): bool {}
    /** @param resource $val */
    function wasm_val_value($val): mixed {}
    /** @param resource $val */
    function wasm_val_kind($val): int {}
    /**
     * @param resource $val
     *
     * @return resource
     */
    function wasm_val_copy($val) {}
    /** @return resource */
    function wasm_val_i32(int $val) {}
    /** @return resource */
    function wasm_val_i64(int $val) {}
    /** @return resource */
    function wasm_val_f32(float $val) {}
    /** @return resource */
    function wasm_val_f64(float $val) {}

    // References

    // TODO(jubianchi): Add ref


    // Frames
    /**
     * @param resource $frame
     *
     * @return resource
     */
    function wasm_frame_copy($frame) {}
    /**
     * @param resource $frame
     *
     * @return resource
     */
    function wasm_frame_instance($frame) {}
    /** @param resource $frame */
    function wasm_frame_func_index($frame): int {}
    /** @param resource $frame */
    function wasm_frame_func_offset($frame): int {}
    /** @param resource $frame */
    function wasm_frame_module_offset($frame): int {}


    // Traps
    /**
     * @param resource $store
     *
     * @return resource
     */
    function wasm_trap_new($store, string $message) {}
    /**
     * @param resource $trap
     *
     * @return resource
     */
    function wasm_trap_copy($trap) {}
    /** @param resource $trap */
    function wasm_trap_message($trap): string {}
    /**
     * @param resource $trap
     *
     * @return resource
     */
    function wasm_trap_origin($trap) {}
    /** @param resource $trap */
    function wasm_trap_trace($trap): \Wasm\Vec\Frame {}


    // Foreign Objects

    // TODO(jubianchi): Add foreign


    // Modules

    /**
     * @param resource $store
     *
     * @return resource
     */
    function wasm_module_new($store, string $wasm) {}
    /** @param resource $module */
    function wasm_module_delete($module): bool {}
    /**
     * @param resource $store
     * @param resource $module
     */
    function wasm_module_validate($store, $module): bool {}
    /** @param resource $module */
    function wasm_module_imports($module): Wasm\Vec\ImportType {}
    /** @param resource $module */
    function wasm_module_exports($module): Wasm\Vec\ExportType {}
    /** @param resource $module */
    function wasm_module_serialize($module): string {}
    /**
     * @param resource $store
     *
     * @return resource
     */
    function wasm_module_deserialize($store, string $wasm) {}
    /**
     * @param resource $module
     */
    function wasm_module_name($module): string {}
    /**
     * @param resource $module
     */
    function wasm_module_set_name($module, string $name): bool {}
    /**
     * @param resource $module
     *
     * @return resource
     */
    function wasm_module_copy($module) {}

    // Function Instances

    /**
     * @param resource $store
     * @param resource $functype
     *
     * @return resource
     */
    function wasm_func_new($store, $functype, callable $func) {}
    /** @param resource $func */
    function wasm_func_delete($func): bool {}
    /**
     * @param resource $func
     *
     * @return resource
     */
    function wasm_func_type($func) {}
    /** @param resource $func */
    function wasm_func_call($func, Wasm\Vec\Val $args): Wasm\Vec\Val {}
    /**
     * @param resource $func
     *
     * @return resource
     */
    function wasm_func_as_extern($func) {}


    // Global Instances

    // TODO(jubianchi): Add global
    /**
     * @param resource $store
     * @param resource $globaltype
     * @param resource $val
     *
     * @return resource
     */
    function wasm_global_new($store, $globaltype, $val) {}
    /** @param resource $global */
    function wasm_global_delete($global): bool {}
    /**
     * @param resource $global
     *
     * @return resource
     */
    function wasm_global_type($global) {}
    /**
     * @param resource $global
     *
     * @return resource
     */
    function wasm_global_get($global) {}
    /**
     * @param resource $global
     * @param resource $val
     */
    function wasm_global_set($global, $val): void {}
    /**
     * @param resource $global
     *
     * @return resource
     */
    function wasm_global_copy($global) {}
    /**
     * @param resource $left
     * @param resource $right
     */
    function wasm_global_same($left, $right): bool {}
    /**
     * @param resource $global
     *
     * @return resource
     */
    function wasm_global_as_extern($global) {}

    // Table Instances

    // TODO(jubianchi): Add table


    // Memory Instances

    // TODO(jubianchi): Add memory


    // Externals
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_kind($extern) {}
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_type($extern) {}
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_as_func($extern) {}
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_as_global($extern) {}
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_as_table($extern) {}
    /**
     * @param resource $extern
     *
     * @return resource
     */
    function wasm_extern_as_memory($extern) {}


    // Module Instances

    /**
     * @param resource $store
     * @param resource $module
     *
     * @return resource
     */
    function wasm_instance_new($store, $module, Wasm\Vec\Extern $externs) {}
    /** @param resource $instance */
    function wasm_instance_delete($instance): bool {}
    /**
     * @param resource $instance
     *
     * @return resource
     */
    function wasm_instance_exports($instance): Wasm\Vec\Extern {}
    /**
     * @param resource $instance
     *
     * @return resource
     */
    function wasm_instance_copy($instance) {}

    ///////////////////////////////////////////////////////////////////////////////
    // Wamser API

    /**
     * Return the version of the Wasmer C API.
     *
     * # Example
     *
     * ```php
     * <?php
     * $version = wasmer_version();
     * ```
     */
    function wasmer_version(): string {}

    /**
     * Return the major version of the Wasmer C API.
     *
     * # Example
     *
     * ```php
     * <?php
     * $major = wasmer_version_major();
     * ```
     */
    function wasmer_version_major(): int {}

    /**
     * Return the minor version of the Wasmer C API.
     *
     * # Example
     *
     * ```php
     * <?php
     * $minor = wasmer_version_minor();
     * ```
     */
    function wasmer_version_minor(): int {}

    /**
     * Return the patch version of the Wasmer C API.
     *
     * # Example
     *
     * ```php
     * <?php
     * $patch = wasmer_version_patch();
     * ```
     */
    function wasmer_version_patch(): int {}

    /**
     * Return the pre-release label of the Wasmer C API.
     *
     * This function will return an empty string if the Wasmer C API is stable.
     *
     * # Example
     *
     * ```php
     * <?php
     * $pre = wasmer_version_pre();
     * ```
     */
    function wasmer_version_pre(): string {}

    /** @return resource */
    function wat2wasm(string $wat): string {}
}
