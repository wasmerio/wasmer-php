<?php

/** @generate-function-entries */

namespace Wasm\Vec {
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

    // Limits

    /** @return resource */
    function wasm_limits_new(int $min, int $max) {}
    /** @param resource $limits */
    function wasm_limits_min($limits): int {}
    /** @param resource $limits */
    function wasm_limits_max($limits): int {}

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
}
