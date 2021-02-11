<?php

declare(strict_types=1);

use Wasm\Module;
use Wasm\Type;

require_once __DIR__.'/../vendor/autoload.php';

echo 'Initializing...'.PHP_EOL;
$engine = Wasm\Engine::new();
$store = Wasm\Store::new($engine);

echo 'Loading WAT...'.PHP_EOL;
$wat = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.basename(__FILE__, '.php').'.wat');

echo 'Loading binary...'.PHP_EOL;
$wasm = Wasm\Wat::wasm($wat);

echo 'Compiling module...'.PHP_EOL;
$module = Wasm\Module::new($store, $wasm);

function check(Module\Val $val, int | float $expected)
{
    $actual = $val->value();

    assert($actual === $expected, sprintf('%s !== %s', var_export($actual, true), var_export($expected, true)));
}

function check_global(Module\Globl $global, int | float $expected)
{
    check($global->get(), $expected);
}

function check_call(Module\Func $func, $expected)
{
    $args = new Wasm\Vec\Val();
    $results = $func($args);

    check(Module\Val::new($results[0]), $expected);
}

function get_export_global(Wasm\Vec\Extern $externs, int $i)
{
    assert(count($externs) > $i);

    return (new Module\Extern($externs[$i]))->asGlobal();
}

function get_export_func(Wasm\Vec\Extern $externs, int $i)
{
    assert(count($externs) > $i);

    return (new Module\Extern($externs[$i]))->asFunc();
}

echo 'Creating globals...'.PHP_EOL;
$const_f32_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
$const_i64_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I64));
$var_f32_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F32), Type\GlobalType::MUTABILITY_VAR);
$var_i64_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I64), Type\GlobalType::MUTABILITY_VAR);

$val_f32_1 = Module\Val::newF32((float) 1);
$const_f32_import = Module\Globl::new($store, $const_f32_type, $val_f32_1);
$val_i64_2 = Module\Val::newI64(2);
$const_i64_import = Module\Globl::new($store, $const_i64_type, $val_i64_2);
$val_f32_3 = Module\Val::newF32((float) 3);
$var_f32_import = Module\Globl::new($store, $var_f32_type, $val_f32_3);
$val_i64_4 = Module\Val::newI64(4);
$var_i64_import = Module\Globl::new($store, $var_i64_type, $val_i64_4);

echo 'Instantiating module...'.PHP_EOL;
$const_f32_extern = $const_f32_import->asExtern();
$const_i64_extern = $const_i64_import->asExtern();
$var_f32_extern = $var_f32_import->asExtern();
$var_i64_extern = $var_i64_import->asExtern();

$externs = new Wasm\Vec\Extern([
    $const_f32_extern->inner(),
    $const_i64_extern->inner(),
    $var_f32_extern->inner(),
    $var_i64_extern->inner(),
]);
$instance = Module\Instance::new($store, $module, $externs);

echo 'Extracting export...'.PHP_EOL;
$exports = $instance->exports();
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

echo 'Try cloning...'.PHP_EOL;
$copy = clone $var_f32_import;
assert($var_f32_import->same($copy));

echo 'Accessing globals...'.PHP_EOL;
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
$var_f32_import->set(33);
$var_i64_import->set(34);
$var_f32_export->set(37);
$var_i64_export->set(38);

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
$set_var_f32_import($args73);
$args74 = new Wasm\Vec\Val([wasm_val_i64(74)]);
$set_var_i64_import($args74);
$args77 = new Wasm\Vec\Val([wasm_val_f32(77)]);
$set_var_f32_export($args77);
$args78 = new Wasm\Vec\Val([wasm_val_i64(78)]);
$set_var_i64_export($args78);

check_global($var_f32_import, (float) 73);
check_global($var_i64_import, 74);
check_global($var_f32_export, (float) 77);
check_global($var_i64_export, 78);

check_call($get_var_f32_import, (float) 73);
check_call($get_var_i64_import, 74);
check_call($get_var_f32_export, (float) 77);
check_call($get_var_i64_export, 78);
