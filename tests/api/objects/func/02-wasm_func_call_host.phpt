--TEST--
Func API: wasm_func_call (host)

--SKIPIF--
<?php

if (true) print 'skip wasm_func_call does not support calling host functions';

?>
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

$func = wasm_func_new($store, $functype, function () { var_dump('Hello from PHP closure'); });
$extern = wasm_func_as_extern($func);
$externs = new Wasm\Vec\Extern([$extern]);
wasm_instance_new($store, $module, $externs);

var_dump(wasm_func_call($func, new Wasm\Vec\Val()));

?>
--EXPECTF--
string(22) "Hello from PHP closure"
