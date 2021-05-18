<?php

/** @generate-function-entries */

///////////////////////////////////////////////////////////////////////////////
// Runtime Environment

// Configuration

/**
 * Create a new default Wasmer configuration.
 *
 * ```php
 * <?php
 *
 * // Create the configuration.
 * $config = wasm_config_new();
 *
 * // Create the engine using the configuration.
 * $engine = wasm_engine_new_with_config($config);
 *
 * // Free everything.
 * wasm_engine_delete($engine);
 * ```
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_config_new()
{
}

/**
 * Delete a config object.
 *
 * ```php
 * <?php
 *
 * // Create the configuration.
 * $config = wasm_config_new();
 *
 * // Delete the configuration
 * wasm_config_delete($config);
 * ```
 *
 * ℹ️ This function does not need to be called if the configuration was used as an argument of
 * `wasm_engine_new_with_config`.
 *
 * @param resource $config A `wasm_config_t` resource
 */
function wasm_config_delete($config): bool
{
}

/**
 * Update the configuration to specify a particular compiler to use.
 *
 * ```php
 * <?php
 *
 * // Create the configuration.
 * $config = wasm_config_new();
 *
 * // Use the Cranelift compiler.
 * wasm_config_set_compiler($config, WASM_COMPILER_CRANELIFT);
 *
 * // Create the engine.
 * $engine = wasm_engine_new_with_config($config);
 *
 * // Free everything.
 * wasm_engine_delete($engine);
 * ```
 *
 * ⚠️ This is a Wasmer-specific function.
 *
 * @param resource $config A `wasm_config_t` resource
 */
function wasm_config_set_compiler($config, int $compiler): bool
{
}

/**
 * Update the configuration to specify a particular engine to use.
 *
 * ```php
 * <?php
 *
 * // Create the configuration.
 * $config = wasm_config_new();
 *
 * // Use the JIT engine.
 * wasm_config_set_compiler($config, WASM_ENGINE_JIT);
 *
 * // Create the engine.
 * $engine = wasm_engine_new_with_config($config);
 *
 * // Free everything.
 * wasm_engine_delete($engine);
 * ```
 *
 * ⚠️ This is a Wasmer-specific function.
 *
 * @param resource $config A `wasm_config_t` resource
 */
function wasm_config_set_engine($config, int $engine): bool
{
}

// Engine

/**
 * Create a new JIT engine with the default compiler.
 *
 * @return resource
 *
 * @see wasm_engine_delete()
 */
function wasm_engine_new()
{
}

/**
 * Create an engine with a particular configuration.
 *
 * @param resource $config A `wasm_config_t` resource
 *
 * @return resource
 *
 * @see wasm_config_new()
 */
function wasm_engine_new_with_config($config)
{
}

/**
 * Delete an engine.
 *
 * ```php
 * <?php
 *
 * // Create a default engine.
 * $engine = wasm_engine_new();
 *
 * // Free everything.
 * wasm_engine_delete($engine);
 * ```
 *
 * @param resource $engine A `wasm_engine_t` resource
 */
function wasm_engine_delete($engine): bool
{
}

// Store

/**
 * Create a new WebAssembly store given a specific engine.
 *
 * ```php
 * <?php
 *
 * // Create a default engine.
 * $engine = wasm_engine_new();
 *
 * // Create a store.
 * $store = wasm_store_new($store);
 *
 * // Free everything.
 * wasm_store_delete($store);
 * wasm_engine_delete($engine);
 * ```
 *
 * @param resource $engine A `wasm_engine_t` resource
 *
 * @return resource
 */
function wasm_store_new($engine)
{
}

/**
 * Delete a store.
 *
 * @param resource $store A `wasm_store_t` resource
 *
 * @see wasm_store_new()
 */
function wasm_store_delete($store): bool
{
}

///////////////////////////////////////////////////////////////////////////////
// Type Representations

// Value Types

/**
 * Create a new valtype.
 *
 * ```php
 * <?php
 *
 * // Create a valtype of kind i32
 * $valtype = wasm_valtype_new(WASM_I32);
 *
 * // Free everything.
 * wasm_valtype_delete($valtype);
 * ```
 *
 * @param int $kind The kind of valuetype.
 *                  This must be one of `WASM_I32`, `WASM_F32`, `WASM_I64`, `WASM_F64`, `WASM_ANYREF` or `WASM_FUNCREF`.
 *
 * @return resource
 */
function wasm_valtype_new(int $kind)
{
}

/**
 * Delete a valtype.
 *
 * @param resource $valtype a `wasm_valtype_t` resource
 *
 * ℹ️ This function does not need to be called if the configuration was used as an argument of
 * `wasm_functype_new`, `wasm_globaltype_new`, `wasm_tabletype_new` or was added to a `\Wasm\Vec\Val`
 *
 * @see wasm_valtype_new()
 */
function wasm_valtype_delete($valtype): bool
{
}

/**
 * Return the valtype kind.
 *
 * ```php
 * <?php
 *
 * // Create a valtype of kind i32
 * $valtype = wasm_valtype_new(WASM_I32);
 *
 * var_dump(wasm_valtype_kind($valtype) === WASM_I32); // bool(true)
 *
 * // Free everything.
 * wasm_valtype_delete($valtype);
 * ```
 *
 * @param resource $valtype A `wasm_valtype_t` resource
 */
function wasm_valtype_kind($valtype): int
{
}

/**
 * Return `true` if the given valtype has a numeric kind.
 *
 * ```php
 * <?php
 *
 * // Create some valtypes
 * $i32 = wasm_valtype_new(WASM_I32);
 * $ref = wasm_valtype_new(WASM_ANYREF);
 *
 * var_dump(wasm_valtype_is_num($i32)); // bool(true)
 * var_dump(wasm_valtype_is_num($ref)); // bool(false)
 *
 * // Free everything.
 * wasm_valtype_delete($i32);
 * wasm_valtype_delete($ref);
 * ```
 *
 * @param resource $valtype A `wasm_valtype_t` resource
 */
function wasm_valtype_is_num($valtype): bool
{
}

/**
 * Return `true` if the given valtype has a reference kind.
 *
 * ```php
 * <?php
 *
 * // Create some valtypes
 * $i32 = wasm_valtype_new(WASM_I32);
 * $ref = wasm_valtype_new(WASM_ANYREF);
 *
 * var_dump(wasm_valtype_is_ref($i32)); // bool(false)
 * var_dump(wasm_valtype_is_ref($ref)); // bool(true)
 *
 * // Free everything.
 * wasm_valtype_delete($i32);
 * wasm_valtype_delete($ref);
 * ```
 *
 * @param resource $valtype A `wasm_valtype_t` resource
 */
function wasm_valtype_is_ref($valtype): bool
{
}

/**
 * Create a copy of the given valtype.
 *
 * ```php
 * <?php
 *
 * // Create a valtype of kind i32
 * $valtype = wasm_valtype_new(WASM_I32);
 * $copy = wasm_valtype_copy($valtype);
 *
 * // Free everything
 * wasm_valtype_delete($copy);
 * wasm_valtype_delete($valtype);
 * ```
 *
 * @param resource $valtype The `wasm_valtype_t` resource to copy
 *
 * @return resource
 */
function wasm_valtype_copy($valtype)
{
}

/**
 * Verify whether the given kind is numeric.
 *
 * ```php
 * <?php
 *
 * var_dump(wasm_valkind_is_num(WASM_I32)); // bool(true)
 * var_dump(wasm_valkind_is_num(WASM_ANYREF)); // bool(false)
 * ```
 *
 * @param int $kind The kind of valuetype.
 *                  This must be one of `WASM_I32`, `WASM_F32`, `WASM_I64`, `WASM_F64`, `WASM_ANYREF` or `WASM_FUNCREF`.
 */
function wasm_valkind_is_num(int $kind): bool
{
}

/**
 * Verify whether the given kind is a reference.
 *
 * ```php
 * <?php
 *
 * var_dump(wasm_valkind_is_ref(WASM_ANYREF)); // bool(true)
 * var_dump(wasm_valkind_is_ref(WASM_I32)); // bool(false)
 * ```
 *
 * @param int $kind The kind of valuetype.
 *                  This must be one of `WASM_I32`, `WASM_F32`, `WASM_I64`, `WASM_F64`, `WASM_ANYREF` or `WASM_FUNCREF`.
 */
function wasm_valkind_is_ref(int $kind): bool
{
}

// Function Types

/**
 * Create a new valtype.
 *
 * ```php
 * <?php
 *
 * use Wasm\Vec\ValType;
 *
 * // Create a valtype of kind i32
 * $functype = wasm_functype_new(new ValType(), new ValType());
 *
 * // Free everything.
 * wasm_functype_delete($functype);
 * ```
 *
 * @return resource
 */
function wasm_functype_new(Wasm\Vec\ValType $params, Wasm\Vec\ValType $results)
{
}

/**
 * Delete a functype.
 *
 * @param resource $functype A `wasm_functype_t` resource
 *
 * @see wasm_functype_new()
 */
function wasm_functype_delete($functype): bool
{
}
/**
 * @param resource $functype A `wasm_functype_t` resource
 */
function wasm_functype_params($functype): Wasm\Vec\ValType
{
}
/**
 * @param resource $functype A `wasm_functype_t` resource
 */
function wasm_functype_results($functype): Wasm\Vec\ValType
{
}
/**
 * @param resource $functype A `wasm_functype_t` resource
 *
 * @return resource
 */
function wasm_functype_copy($functype)
{
}
/**
 * @param resource $functype A `wasm_functype_t` resource
 *
 * @return resource
 */
function wasm_functype_as_externtype($functype)
{
}

// Global Types

/**
 * @param resource $valtype A `wasm_valtype_t` resource
 *
 * @return resource
 */
function wasm_globaltype_new($valtype, int $mutability)
{
}

/**
 * Delete a globaltype.
 *
 * @param resource $globaltype A `wasm_globaltype_t` resource
 *
 * @see wasm_globaltype_new()
 */
function wasm_globaltype_delete($globaltype): bool
{
}

/**
 * @param resource $globaltype
 *
 * @return resource
 */
function wasm_globaltype_content($globaltype)
{
}

/**
 * @param resource $globaltype A `wasm_globaltype_t` resource
 */
function wasm_globaltype_mutability($globaltype): int
{
}

/**
 * @param resource $globaltype A `wasm_globaltype_t` resource
 *
 * @return resource
 */
function wasm_globaltype_copy($globaltype)
{
}
/**
 * @param resource $globaltype A `wasm_globaltype_t` resource
 *
 * @return resource
 */
function wasm_globaltype_as_externtype($globaltype)
{
}

// Limits

/**
 * Create a new limits.
 *
 * @return resource
 *
 * @see wasm_memorytype_new()
 * @see wasm_tabletype_new()
 */
function wasm_limits_new(int $min, int $max)
{
}

/**
 * @param resource $limits A `wasm_limits_t` resource
 */
function wasm_limits_min($limits): int
{
}

/**
 * @param resource $limits A `wasm_limits_t` resource
 */
function wasm_limits_max($limits): int
{
}

// Table Types

/**
 * @param resource $valtype A `wasm_valtype_t` resource
 * @param resource $limits  A `wasm_limits_t` resource
 *
 * @return resource
 */
function wasm_tabletype_new($valtype, $limits)
{
}

/**
 * Delete a tabletype.
 *
 * @param resource $tabletype A `wasm_tabletype_t` resource
 *
 * @see wasm_tabletype_new()
 */
function wasm_tabletype_delete($tabletype): bool
{
}

/**
 * @param resource $tabletype A `wasm_tabletype_t` resource
 *
 * @return resource
 */
function wasm_tabletype_element($tabletype)
{
}

/**
 * @param resource $tabletype A `wasm_tabletype_t` resource
 *
 * @return resource
 */
function wasm_tabletype_limits($tabletype)
{
}

/**
 * @param resource $tabletype A `wasm_tabletype_t` resource
 *
 * @return resource
 */
function wasm_tabletype_copy($tabletype)
{
}

/**
 * @param resource $tabletype A `wasm_tabletype_t` resource
 *
 * @return resource
 */
function wasm_tabletype_as_externtype($tabletype)
{
}

// Memory Types

/**
 * Create a new memorytype.
 *
 * ```php
 * <?php
 *
 * $limits = wasm_limits_new(1, 2);
 * $memorytype = wasm_memorytype_new($limits);
 *
 * // Free everything
 * wasm_memorytype_delete($memorytype);
 * ```
 *
 * @param resource $limits A `wasm_limits_t` resource
 *
 * @return resource
 */
function wasm_memorytype_new($limits)
{
}

/**
 * Delete a memorytype.
 *
 * @param resource $memorytype A `wasm_memorytype_t` resource
 *
 * @see wasm_memorytype_new()
 */
function wasm_memorytype_delete($memorytype): bool
{
}

/**
 * @param resource $memorytype A `wasm_memorytype_t` resource
 *
 * @return resource
 */
function wasm_memorytype_limits($memorytype)
{
}

/**
 * @param resource $memorytype A `wasm_memorytype_t` resource
 *
 * @return resource
 */
function wasm_memorytype_copy($memorytype)
{
}

/**
 * @param resource $memorytype A `wasm_memorytype_t` resource
 *
 * @return resource
 */
function wasm_memorytype_as_externtype($memorytype)
{
}

// Extern Types

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 */
function wasm_externtype_delete($externtype): bool
{
}

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 */
function wasm_externtype_kind($externtype): int
{
}

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_externtype_as_functype($externtype)
{
}

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_externtype_as_globaltype($externtype)
{
}

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_externtype_as_tabletype($externtype)
{
}

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_externtype_as_memorytype($externtype)
{
}

// Import Types

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @return resource
 */
function wasm_importtype_new(string $module, string $name, $externtype)
{
}

/**
 * Delete a importtype.
 *
 * @param resource $importtype A `wasm_importtype_t` resource
 *
 * @see wasm_importtype_new()
 */
function wasm_importtype_delete($importtype): bool
{
}

/**
 * @param resource $importtype A `wasm_importtype_t` resource
 */
function wasm_importtype_module($importtype): string
{
}

/**
 * @param resource $importtype A `wasm_importtype_t` resource
 */
function wasm_importtype_name($importtype): string
{
}

/**
 * @param resource $importtype A `wasm_importtype_t` resource
 *
 * @return resource
 */
function wasm_importtype_type($importtype)
{
}

/**
 * @param resource $importtype A `wasm_importtype_t` resource
 *
 * @return resource
 */
function wasm_importtype_copy($importtype)
{
}

// Export Types

/**
 * @param resource $externtype A `wasm_externtype_t` resource
 *
 * @return resource
 */
function wasm_exporttype_new(string $name, $externtype)
{
}

/**
 * Delete a exporttype.
 *
 * @param resource $exporttype A `wasm_exporttype_t` resource
 *
 * @see wasm_exporttype_new()
 */
function wasm_exporttype_delete($exporttype): bool
{
}

/**
 * @param resource $exporttype A `wasm_exporttype_t` resource
 */
function wasm_exporttype_name($exporttype): string
{
}

/**
 * @param resource $exporttype A `wasm_exporttype_t` resource
 *
 * @return resource
 */
function wasm_exporttype_type($exporttype)
{
}

/**
 * @param resource $exporttype A `wasm_exporttype_t` resource
 *
 * @return resource
 */
function wasm_exporttype_copy($exporttype)
{
}

///////////////////////////////////////////////////////////////////////////////
// Runtime Objects

//Values

/**
 * Delete a val.
 *
 * @param resource $val A `wasm_eval_t` resource
 */
function wasm_val_delete($val): bool
{
}

/**
 * @param resource $val A `wasm_eval_t` resource
 */
function wasm_val_value($val): mixed
{
}

/**
 * @param resource $val A `wasm_eval_t` resource
 */
function wasm_val_kind($val): int
{
}

/**
 * @param resource $val A `wasm_eval_t` resource
 *
 * @return resource
 */
function wasm_val_copy($val)
{
}

/**
 * @return resource
 */
function wasm_val_i32(int $val)
{
}

/**
 * @return resource
 */
function wasm_val_i64(int $val)
{
}

/**
 * @return resource
 */
function wasm_val_f32(float $val)
{
}

/**
 * @return resource
 */
function wasm_val_f64(float $val)
{
}

// References

// TODO(jubianchi): Add ref

// Frames

/**
 * @param resource $frame A `wasm_frame_t` resource
 *
 * @return resource
 */
function wasm_frame_copy($frame)
{
}

/**
 * @param resource $frame A `wasm_frame_t` resource
 *
 * @return resource
 */
function wasm_frame_instance($frame)
{
}

/**
 * @param resource $frame A `wasm_frame_t` resource
 */
function wasm_frame_func_index($frame): int
{
}

/**
 * @param resource $frame A `wasm_frame_t` resource
 */
function wasm_frame_func_offset($frame): int
{
}

/**
 * @param resource $frame A `wasm_frame_t` resource
 */
function wasm_frame_module_offset($frame): int
{
}

// Traps

/**
 * @param resource $store A `wasm_store_t` resource
 *
 * @throw \Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_trap_new($store, string $message)
{
}

/**
 * @param resource $trap A `wasm_trap_t` resource
 */
function wasm_trap_delete($trap): bool
{
}

/**
 * @param resource $trap A `wasm_trap_t` resource
 *
 * @return resource
 */
function wasm_trap_copy($trap)
{
}

/**
 * @param resource $trap A `wasm_trap_t` resource
 */
function wasm_trap_message($trap): string
{
}

/**
 * @param resource $trap A `wasm_trap_t` resource
 *
 * @return resource
 */
function wasm_trap_origin($trap)
{
}

/**
 * @param resource $trap A `wasm_trap_t` resource
 */
function wasm_trap_trace($trap): Wasm\Vec\Frame
{
}

// Foreign Objects

// TODO(jubianchi): Add foreign

// Modules

/**
 * @param resource $store A `wasm_store_t` resource
 *
 * @throw \Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_module_new($store, string $wasm)
{
}

/**
 * Delete a module.
 *
 * @param resource $module A `wasm_module_t` resource
 *
 * @see wasm_module_new()
 */
function wasm_module_delete($module): bool
{
}

/**
 * @param resource $store A `wasm_store_t` resource
 *
 * @throw \Wasm\Exception\RuntimeException
 */
function wasm_module_validate($store, string $wasm): bool
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 */
function wasm_module_imports($module): Wasm\Vec\ImportType
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 */
function wasm_module_exports($module): Wasm\Vec\ExportType
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 */
function wasm_module_serialize($module): string
{
}

/**
 * @param resource $store A `wasm_store_t` resource
 *
 * @throw \Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_module_deserialize($store, string $wasm)
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 */
function wasm_module_name($module): string
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 */
function wasm_module_set_name($module, string $name): bool
{
}

/**
 * @param resource $module A `wasm_module_t` resource
 *
 * @return resource
 */
function wasm_module_copy($module)
{
}

// Function Instances

/**
 * @param resource $store    A `wasm_store_t` resource
 * @param resource $functype A `wasm_functype_t` resource
 *
 * @return resource
 */
function wasm_func_new($store, $functype, callable $func)
{
}

/**
 * Delete a func.
 *
 * @param resource $func A `wasm_func_t` resource
 *
 * @see wasm_func_new()
 */
function wasm_func_delete($func): bool
{
}

/**
 * @param resource $func A `wasm_func_t` resource
 *
 * @return resource
 */
function wasm_func_type($func)
{
}

/**
 * @param resource $func A `wasm_func_t` resource
 */
function wasm_func_param_arity($func): int
{
}

/**
 * @param resource $func A `wasm_func_t` resource
 */
function wasm_func_result_arity($func): int
{
}

/**
 * @param resource $func A `wasm_func_t` resource
 */
function wasm_func_call($func, Wasm\Vec\Val $args): Wasm\Vec\Val
{
}

/**
 * @param resource $func A `wasm_func_t` resource
 *
 * @return resource
 */
function wasm_func_as_extern($func)
{
}

// Global Instances

/**
 * @param resource $store      A `wasm_store_t` resource
 * @param resource $globaltype A `wasm_globaltype_t` resource
 * @param resource $val        A `wasm_val_t` resource
 *
 * @return resource
 */
function wasm_global_new($store, $globaltype, $val)
{
}

/**
 * Delete a global.
 *
 * @param resource $global A `wasm_global_t` resource
 *
 * @see wasm_global_new()
 */
function wasm_global_delete($global): bool
{
}

/**
 * @param resource $global A `wasm_global_t` resource
 *
 * @return resource
 */
function wasm_global_type($global)
{
}

/**
 * @param resource $global A `wasm_global_t` resource
 *
 * @return resource
 */
function wasm_global_get($global)
{
}

/**
 * @param resource $global A `wasm_global_t` resource
 * @param resource $val    A `wasm_val_t` resource
 */
function wasm_global_set($global, $val): void
{
}

/**
 * @param resource $global A `wasm_global_t` resource
 *
 * @return resource
 */
function wasm_global_copy($global)
{
}

/**
 * @param resource $left  A `wasm_global_t` resource
 * @param resource $right A `wasm_global_t` resource
 */
function wasm_global_same($left, $right): bool
{
}

/**
 * @param resource $global A `wasm_global_t` resource
 *
 * @return resource
 */
function wasm_global_as_extern($global)
{
}

// Table Instances

// TODO(jubianchi): Add table

// Memory Instances

/**
 * @param resource $store      A `wasm_store_t` resource
 * @param resource $memorytype A `wasm_memorytype_t` resource
 *
 * @return resource
 */
function wasm_memory_new($store, $memorytype)
{
}

/**
 * Delete a memory.
 *
 * @param resource $memory A `wasm_memory_t` resource
 *
 * @see wasm_memory_new()
 */
function wasm_memory_delete($memory): bool
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 *
 * @return resource
 */
function wasm_memory_type($memory)
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 */
function wasm_memory_data_size($memory): int
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 */
function wasm_memory_size($memory): int
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 */
function wasm_memory_grow($memory, int $delta): bool
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 */
function wasm_memory_data($memory): Wasm\MemoryView
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 *
 * @return resource
 */
function wasm_memory_copy($memory)
{
}

/**
 * @param resource $left  A `wasm_memory_t` resource
 * @param resource $right A `wasm_memory_t` resource
 */
function wasm_memory_same($left, $right): bool
{
}

/**
 * @param resource $memory A `wasm_memory_t` resource
 *
 * @return resource
 */
function wasm_memory_as_extern($memory)
{
}

// Externals

/**
 * Delete an extern.
 *
 * @param resource $extern A `wasm_extern_t` resource
 */
function wasm_extern_delete($extern): bool
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 */
function wasm_extern_kind($extern): int
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 *
 * @return resource
 */
function wasm_extern_type($extern)
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_extern_as_func($extern)
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_extern_as_global($extern)
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_extern_as_table($extern)
{
}

/**
 * @param resource $extern A `wasm_extern_t` resource
 *
 * @throw Wasm\Exception\RuntimeException
 *
 * @return resource
 */
function wasm_extern_as_memory($extern)
{
}

// Module Instances

/**
 * @param resource $store  A `wasm_store_t` resource
 * @param resource $module A `wasm_module_t` resource
 *
 * @return resource
 */
function wasm_instance_new($store, $module, Wasm\Vec\Extern $externs)
{
}

/**
 * Delete an instance.
 *
 * @param resource $instance A `wasm_instance_t` resource
 *
 * @see wasm_instance_new()
 */
function wasm_instance_delete($instance): bool
{
}

/**
 * @param resource $instance A `wasm_instance_t` resource
 */
function wasm_instance_exports($instance): Wasm\Vec\Extern
{
}

/**
 * @param resource $instance A `wasm_instance_t` resource
 *
 * @return resource
 */
function wasm_instance_copy($instance)
{
}

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
 *
 * ⚠️ This is a Wasmer-specific function.
 */
function wasmer_version(): string
{
}

/**
 * Return the major version of the Wasmer C API.
 *
 * # Example
 *
 * ```php
 * <?php
 * $major = wasmer_version_major();
 * ```
 *
 * ⚠️ This is a Wasmer-specific function.
 */
function wasmer_version_major(): int
{
}

/**
 * Return the minor version of the Wasmer C API.
 *
 * # Example
 *
 * ```php
 * <?php
 * $minor = wasmer_version_minor();
 * ```
 *
 * ⚠️ This is a Wasmer-specific function.
 */
function wasmer_version_minor(): int
{
}

/**
 * Return the patch version of the Wasmer C API.
 *
 * # Example
 *
 * ```php
 * <?php
 * $patch = wasmer_version_patch();
 * ```
 *
 * ⚠️ This is a Wasmer-specific function.
 */
function wasmer_version_patch(): int
{
}

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
 *
 * ⚠️ This is a Wasmer-specific function.
 */
function wasmer_version_pre(): string
{
}

/**
 * ⚠️ This is a Wasmer-specific function.
 *
 * @throw Wasm\Exception\RuntimeException
 */
function wat2wasm(string $wat): string
{
}
