# The `Wasm` library

The `Wasm` library is a layer on top of the `php-ext-wasm` extension
that brings more safety and a more user-friendly API.

This section lists API provided by the `Wasm` library. The entry is
`Wasm\Instance`.

## Quick example

Let's go through a very quick example. Let's assume this Rust program:

```rust
#[no_mangle]
pub extern fn sum(x: i32, y: i32) -> i32 {
    x + y
}
```

Once compiled to a WebAssembly binary named `my_program.wasm`, it is
possible to run within PHP as follows:

```php,ignore
$instance = new Wasm\Instance('my_program.wasm');
$result = $instance->sum(1, 2);

var_dump($result); // int(3)
```

This example above calls a function `sum` that is exported from
`my_program.wasm`.

## The life pattern of a WebAssembly binary, and the API

A WebAssembly file is only a sequence of bytes. In order to get a
running WebAssembly program:

 1. These bytes must be compiled into a module,
 2. The module must be instantiate.
 
The `Wasm\Module` represents a module. The `Wasm\Instance` represents
an instance of a module.

So, one can write:

```php
$module = new Wasm\Module('my_program.wasm');
$instance = $module->instantiate();
$result = $instance->sum(1, 2);
```

Or, alternatively, when having a module is not necessary, one can
write:

```php
$instance = new Wasm\Instance('my_program.wasm');
$result = $instance->sum(1, 2);
```

Why would one want to get a module? Because it's serializable, and
thus can be stored in a cache. The bytes compilation to a module can
be costly depending of the size of your WebAssembly program, and the
[runtime
backend](https://github.com/wasmerio/wasmer/tree/master/lib#backends)
(LLVM, Cranelift etc.).

# The `php-ext-wasm` raw API

This section presents the raw API provided by the `php-ext-wasm`
extension. The entire `Wasm` library is based on this API.

### Function `wasm_read_bytes`

Reads bytes from a WebAssembly file:

```php
$bytes = wasm_read_bytes('my_program.wasm');
```

This function returns a resource of type `wasm_bytes`.

### Function `wasm_validate`

Validates bytes from the `wasm_read_bytes` function:

```php
$bytes = wasm_read_bytes('my_program.wasm');

if (false === wasm_validate($bytes)) {
    echo 'The program seems corrupted.';
}
```

This function returns `true` when the bytes are valid, `false`
otherwise.

### Function `wasm_compile`

Compiles bytes into a WebAssembly module.

```php
$bytes = wasm_read_bytes('my_program.wasm');
$module = wasm_compile($bytes);
```

This function returns a resource of type `wasm_module`.

### Function `wasm_module_serialize`

Serializes a module into a PHP string (technically a sequence of
bytes):

```php
$bytes = wasm_read_bytes('my_program.wasm');
$module = wasm_compile($bytes);
$serialized_module = wasm_module_serialize($module);
```

This function returns a string.

### Function `wasm_module_deserialize`

Deserializes a module from a PHP string (technically a sequence of
bytes):

```php
$bytes = wasm_read_bytes('my_program.wasm');
$module = wasm_compile($bytes);
$serialized_module = wasm_module_serialize($module);
unset($module);

$module = wasm_module_deserialize($module);
$instance = wasm_module_new_instance($module);
// life continues.
```

This function returns a resource of type `wasm_module`.

### Function `wasm_module_new_instance`

Instantiates a WebAssembly module:

```php
$bytes = wasm_read_bytes('my_program.wasm');
$module = wasm_compile($bytes);
$instance = wasm_module_new_instance($module);
```

This function returns a resource of type `wasm_instance`.

### Function `wasm_new_instance`

Compiles and instantiates WebAssembly bytes:

```php
$bytes = wasm_read_bytes('my_program.wasm');
$instance = wasm_new_instance($bytes);
```

This function returns a resource of type `wasm_instance`.

This function combines `wasm_compile` and
`wasm_module_new_instance`. It “hides” the module.

### Function `wasm_get_function_signature`

Returns the signature of an exported function:

```php
$bytes = wasm_read_bytes('my_program.wasm');
$instance = wasm_new_instance($bytes);
$signature = wasm_get_function_signature($instance, 'function_name');
```

This function returns an array of `WASM_TYPE_*` constants. The first
entries are for the inputs, the last entry is for the output.

### Function `wasm_value`

Compile a PHP value into a WebAssembly value:

```php
$value = wasm_value(WASM_TYPE_I32, 7);
```

This function returns a resource of type `wasm_value`.

### Function `wasm_invoke_function`

Invokes a function that lives in the WebAssembly program.

```php
$bytes = wasm_read_bytes('my_program.wasm');
$instance = wasm_new_instance($bytes);

// sum(1, 2)
$result = wasm_invoke_function(
    $instance,
    'sum',
    [
        wasm_value(WASM_TYPE_I32, 1),
        wasm_value(WASM_TYPE_I32, 2),
    ]
);
```

This function returns the result of the invoked function.

### Function `wasm_get_last_error`

Reads the last error if any:

```php
$bytes = wasm_read_bytes('my_program.wasm');
$instance = wasm_new_instance($bytes);

// sum(1) — one argument is missing!
$result = wasm_invoke_function(
    $instance,
    'sum',
    [wasm_value(WASM_TYPE_I32, 1)]
);

if (false === $result) {
    echo wasm_get_last_error();
    // Call error: Parameters of type [I32] did not match signature [I32, I32] -> [I32]
}
```

This function returns the error message if any.
