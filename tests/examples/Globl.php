<?php

declare(strict_types=1);

namespace Wasm\Examples;

use Wasm;
use Wasm\Type;

/**
 * @medium
 */
final class Globl extends Example
{
    /**
     * @test
     */
    public function main()
    {
        // Initializing...
        $engine = Wasm\Engine::new();
        $store = Wasm\Store::new($engine);

        // Loading WAT...
        $wat = file_get_contents($this->module());

        // Loading binary...
        $wasm = Wasm\Wat::wasm($wat);

        // Compiling module...
        $module = Wasm\Module::new($store, $wasm);

        // Creating globals...
        $const_f32_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I32));
        $const_i64_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I64));
        $var_f32_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_F32), Type\GlobalType::MUTABILITY_VAR);
        $var_i64_type = Type\GlobalType::new(Type\ValType::new(Type\ValType::KIND_I64), Type\GlobalType::MUTABILITY_VAR);

        $val_f32_1 = Wasm\Val::newF32((float) 1);
        $const_f32_import = Wasm\Globl::new($store, $const_f32_type, $val_f32_1);
        $val_i64_2 = Wasm\Val::newI64(2);
        $const_i64_import = Wasm\Globl::new($store, $const_i64_type, $val_i64_2);
        $val_f32_3 = Wasm\Val::newF32((float) 3);
        $var_f32_import = Wasm\Globl::new($store, $var_f32_type, $val_f32_3);
        $val_i64_4 = Wasm\Val::newI64(4);
        $var_i64_import = Wasm\Globl::new($store, $var_i64_type, $val_i64_4);

        // Instantiating module...
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
        $instance = Wasm\Instance::new($store, $module, $externs);

        // Extracting export...
        $exports = $instance->exports();
        $i = 0;
        $const_f32_export = self::get_export_global($exports, $i++);
        $const_i64_export = self::get_export_global($exports, $i++);
        $var_f32_export = self::get_export_global($exports, $i++);
        $var_i64_export = self::get_export_global($exports, $i++);
        $get_const_f32_import = self::get_export_func($exports, $i++);
        $get_const_i64_import = self::get_export_func($exports, $i++);
        $get_var_f32_import = self::get_export_func($exports, $i++);
        $get_var_i64_import = self::get_export_func($exports, $i++);
        $get_const_f32_export = self::get_export_func($exports, $i++);
        $get_const_i64_export = self::get_export_func($exports, $i++);
        $get_var_f32_export = self::get_export_func($exports, $i++);
        $get_var_i64_export = self::get_export_func($exports, $i++);
        $set_var_f32_import = self::get_export_func($exports, $i++);
        $set_var_i64_import = self::get_export_func($exports, $i++);
        $set_var_f32_export = self::get_export_func($exports, $i++);
        $set_var_i64_export = self::get_export_func($exports, $i++);

        // Try cloning...
        $copy = clone $var_f32_import;
        self::assertTrue($var_f32_import->same($copy));

        // Accessing globals...
        self::check_global($const_f32_import, (float) 1);
        self::check_global($const_i64_import, 2);
        self::check_global($var_f32_import, (float) 3);
        self::check_global($var_i64_import, 4);
        self::check_global($const_f32_export, (float) 5);
        self::check_global($const_i64_export, 6);
        self::check_global($var_f32_export, (float) 7);
        self::check_global($var_i64_export, 8);

        self::check_call($get_const_f32_import, (float) 1);
        self::check_call($get_const_i64_import, 2);
        self::check_call($get_var_f32_import, (float) 3);
        self::check_call($get_var_i64_import, 4);
        self::check_call($get_const_f32_export, (float) 5);
        self::check_call($get_const_i64_export, 6);
        self::check_call($get_var_f32_export, (float) 7);
        self::check_call($get_var_i64_export, 8);

        // Modify variables through API and check again.
        $var_f32_import->set(33);
        $var_i64_import->set(34);
        $var_f32_export->set(37);
        $var_i64_export->set(38);

        self::check_global($var_f32_import, (float) 33);
        self::check_global($var_i64_import, 34);
        self::check_global($var_f32_export, (float) 37);
        self::check_global($var_i64_export, 38);

        self::check_call($get_var_f32_import, (float) 33);
        self::check_call($get_var_i64_import, 34);
        self::check_call($get_var_f32_export, (float) 37);
        self::check_call($get_var_i64_export, 38);

        // Modify variables through calls and check again.
        $args73 = new Wasm\Vec\Val([wasm_val_f32(73)]);
        $set_var_f32_import($args73);
        $args74 = new Wasm\Vec\Val([wasm_val_i64(74)]);
        $set_var_i64_import($args74);
        $args77 = new Wasm\Vec\Val([wasm_val_f32(77)]);
        $set_var_f32_export($args77);
        $args78 = new Wasm\Vec\Val([wasm_val_i64(78)]);
        $set_var_i64_export($args78);

        self::check_global($var_f32_import, (float) 73);
        self::check_global($var_i64_import, 74);
        self::check_global($var_f32_export, (float) 77);
        self::check_global($var_i64_export, 78);

        self::check_call($get_var_f32_import, (float) 73);
        self::check_call($get_var_i64_import, 74);
        self::check_call($get_var_f32_export, (float) 77);
        self::check_call($get_var_i64_export, 78);
    }

    public static function check(Wasm\Val $val, int | float $expected): void
    {
        $actual = $val->value();

        self::assertTrue($actual === $expected, sprintf('%s !== %s', var_export($actual, true), var_export($expected, true)));
    }

    public static function check_global(Wasm\Globl $global, int | float $expected): void
    {
        self::check($global->get(), $expected);
    }

    public static function check_call(Wasm\Func $func, $expected): void
    {
        $args = new Wasm\Vec\Val();
        $results = $func($args);

        self::check(Wasm\Val::new($results[0]), $expected);
    }

    public static function get_export_global(Wasm\Vec\Extern $externs, int $i): Wasm\Globl
    {
        self::assertTrue(count($externs) > $i);

        return (new Wasm\Extern($externs[$i]))->asGlobal();
    }

    public static function get_export_func(Wasm\Vec\Extern $externs, int $i): Wasm\Func
    {
        self::assertTrue(count($externs) > $i);

        return (new Wasm\Extern($externs[$i]))->asFunc();
    }
}
