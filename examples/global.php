<?php

echo 'Initializing...' . PHP_EOL;
$engine = wasm_engine_new();
$store = wasm_store_new($engine);

echo 'Loading WAT...' . PHP_EOL;
$wat = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . basename(__FILE__, '.php') . '.wat');

echo 'Loading binary...' . PHP_EOL;
$wasm = wat2wasm($wat);

echo 'Compiling module...' . PHP_EOL;
$module = wasm_module_new($store, $wasm);

function check($val, int|float $expected) {
    $actual = wasm_val_value($val);

    assert($actual === $expected, sprintf("%s !== %s", var_export($actual, true), var_export($expected, true)));
}

function check_global($global, int|float $expected) {
    check(wasm_global_get($global), $expected);
}

function check_call($func, $expected) {
    $args = new Wasm\Vec\Val();
    $results = wasm_func_call($func, $args);

    check($results[0], $expected);
}

function get_export_global(Wasm\Vec\Extern $externs, int $i) {
    assert(count($externs) > $i);

    return wasm_extern_as_global($externs[$i]);
}

function get_export_func(Wasm\Vec\Extern $externs, int $i) {
    assert(count($externs) > $i);

    return wasm_extern_as_func($externs[$i]);
}

echo 'Creating globals...' . PHP_EOL;
$const_f32_type = wasm_globaltype_new(wasm_valtype_new(WASM_F32), WASM_CONST);
$const_i64_type = wasm_globaltype_new(wasm_valtype_new(WASM_I64), WASM_CONST);
$var_f32_type = wasm_globaltype_new(wasm_valtype_new(WASM_F32), WASM_VAR);
$var_i64_type = wasm_globaltype_new(wasm_valtype_new(WASM_I64), WASM_VAR);

$val_f32_1 = wasm_val_f32((float) 1);
$const_f32_import = wasm_global_new($store, $const_f32_type, $val_f32_1);
$val_i64_2 = wasm_val_i64(2);
$const_i64_import = wasm_global_new($store, $const_i64_type, $val_i64_2);
$val_f32_3 = wasm_val_f32((float) 3);
$var_f32_import = wasm_global_new($store, $var_f32_type, $val_f32_3);
$val_i64_4 = wasm_val_i64(4);
$var_i64_import = wasm_global_new($store, $var_i64_type, $val_i64_4);

wasm_globaltype_delete($const_f32_type);
wasm_globaltype_delete($const_i64_type);
wasm_globaltype_delete($var_f32_type);
wasm_globaltype_delete($var_i64_type);

echo 'Instantiating module...' . PHP_EOL;
$externs = new Wasm\Vec\Extern([
    wasm_global_as_extern($const_f32_import),
    wasm_global_as_extern($const_i64_import),
    wasm_global_as_extern($var_f32_import),
    wasm_global_as_extern($var_i64_import)
]);
$instance = wasm_instance_new($store, $module, $externs);

wasm_module_delete($module);

echo 'Extracting export...' . PHP_EOL;
$exports = wasm_instance_exports($instance);
$i = 0;
$const_f32_export = get_export_global($exports, $i++);
$const_i64_export = get_export_global($exports, $i++);
$var_f32_export = get_export_global($exports, $i++);
$var_i64_export = get_export_global($exports, $i++);
$get_const_f32_import = get_export_func($exports, $i++);
$get_const_i64_import = get_export_func($exports, $i++);
$get_var_f32_import = get_export_func($exports, $i++);
$get_var_i64_import = get_export_func($exports, $i++);
$get_const_f32_export = get_export_func($exports, $i++);
$get_const_i64_export = get_export_func($exports, $i++);
$get_var_f32_export = get_export_func($exports, $i++);
$get_var_i64_export = get_export_func($exports, $i++);
$set_var_f32_import = get_export_func($exports, $i++);
$set_var_i64_import = get_export_func($exports, $i++);
$set_var_f32_export = get_export_func($exports, $i++);
$set_var_i64_export = get_export_func($exports, $i++);

echo 'Try cloning...' . PHP_EOL;
$copy = wasm_global_copy($var_f32_import);
assert(wasm_global_same($var_f32_import, $copy));
wasm_global_delete($copy);

echo 'Accessing globals...' . PHP_EOL;
check_global($const_f32_import, (float) 1);
check_global($const_i64_import, 2);
check_global($var_f32_import, (float) 3);
check_global($var_i64_import, 4);
check_global($const_f32_export, (float) 5);
check_global($const_i64_export, 6);
check_global($var_f32_export, (float) 7);
check_global($var_i64_export, 8);

check_call($get_const_f32_import, (float) 1);
check_call($get_const_i64_import, 2);
check_call($get_var_f32_import, (float) 3);
check_call($get_var_i64_import, 4);
check_call($get_const_f32_export, (float) 5);
check_call($get_const_i64_export, 6);
check_call($get_var_f32_export, (float) 7);
check_call($get_var_i64_export, 8);

// Modify variables through API and check again.
$val33 = wasm_val_f32(33);
wasm_global_set($var_f32_import, $val33);
$val34 = wasm_val_i64(34);
wasm_global_set($var_i64_import, $val34);
$val37 = wasm_val_f32(37);
wasm_global_set($var_f32_export, $val37);
$val38 = wasm_val_i64(38);
wasm_global_set($var_i64_export, $val38);

check_global($var_f32_import, (float) 33);
check_global($var_i64_import, 34);
check_global($var_f32_export, (float) 37);
check_global($var_i64_export, 38);

check_call($get_var_f32_import, (float) 33);
check_call($get_var_i64_import, 34);
check_call($get_var_f32_export, (float) 37);
check_call($get_var_i64_export, 38);

// Modify variables through calls and check again.
$args73 = new Wasm\Vec\Val([wasm_val_f32(73)]);
wasm_func_call($set_var_f32_import, $args73);
$args74 = new Wasm\Vec\Val([wasm_val_i64(74)]);
wasm_func_call($set_var_i64_import, $args74);
$args77 = new Wasm\Vec\Val([wasm_val_f32(77)]);
wasm_func_call($set_var_f32_export, $args77);
$args78 = new Wasm\Vec\Val([wasm_val_i64(78)]);
wasm_func_call($set_var_i64_export, $args78);

check_global($var_f32_import, (float) 73);
check_global($var_i64_import, 74);
check_global($var_f32_export, (float) 77);
check_global($var_i64_export, 78);

check_call($get_var_f32_import, (float) 73);
check_call($get_var_i64_import, 74);
check_call($get_var_f32_export, (float) 77);
check_call($get_var_i64_export, 78);

wasm_global_delete($const_f32_import);
wasm_global_delete($const_i64_import);
wasm_global_delete($var_f32_import);
wasm_global_delete($var_i64_import);
wasm_instance_delete($instance);

echo 'Shutting down...' . PHP_EOL;
wasm_store_delete($store);
wasm_engine_delete($engine);