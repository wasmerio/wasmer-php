<?php

/** @generate-function-entries */

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
