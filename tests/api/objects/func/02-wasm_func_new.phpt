--TEST--
Func API: wasm_func_new

--FILE--
<?php

$wat = <<<'WAT'
(module
    (func $host_function (import "" "host_function"))
    (start $host_function)
)
WAT;

$engine = wasm_engine_new();
$store = wasm_store_new($engine);
$wasm = wat2wasm($wat);
$module = wasm_module_new($store, $wasm);

$functype = wasm_functype_new(new Wasm\Vec\ValType(), new Wasm\Vec\ValType());

var_dump('Hello from PHP function');
$func = wasm_func_new($store, $functype, "gc_enable");
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);

function foo() { var_dump('Hello from PHP user function'); }
$func = wasm_func_new($store, $functype, "foo");
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);


$cl = function () { var_dump('Hello from PHP named closure'); };
$func = wasm_func_new($store, $functype, $cl);
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);

$func = wasm_func_new($store, $functype, function () { var_dump('Hello from PHP closure'); });
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);

class Invokable {
    public function __invoke() {
        var_dump('Hello from invokable class');
    }
}

$func = wasm_func_new($store, $functype, [new Invokable, '__invoke']);
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);

class Sandbox {
    public function run($store, $module, $functype) {
        $func = wasm_func_new($store, $functype, function () { var_dump('Hello from PHP bound closure'); });
        $extern = wasm_func_as_extern($func);
        $externs = new Wasm\Vec\Extern([$extern]);
        wasm_instance_new($store, $module, $externs);
    }
}

(new Sandbox())->run($store, $module, $functype);

?>
--EXPECTF--
string(23) "Hello from PHP function"
string(28) "Hello from PHP user function"
string(28) "Hello from PHP named closure"
string(22) "Hello from PHP closure"
string(26) "Hello from invokable class"
string(28) "Hello from PHP bound closure"
