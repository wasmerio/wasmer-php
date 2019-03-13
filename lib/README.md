# The `Wasm` library

The `Wasm` library is a layer on top of the `php-ext-wasm` extension
that brings more safety and a more user-friendly API.

## `Wasm` API

This documentation lists API provided by the `Wasm` namespace. The
entry is `Wasm\Instance`.

Let's go through a very basic example. Let's assume this Rust program:

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

## Raw API

This section presents the raw API provided by the `php-ext-wasm`
extension. The entire `Wasm` library is based on this raw API.

### Function `wasm_read_bytes`

Reads bytes from a WebAssembly file:

```php
$bytes = wasm_read_bytes('my_program.wasm');
```

This function returns a resource of type `wasm_bytes`.

### Function `wasm_new_instance`

Compiles and instantiates WebAssembly bytes:

```php
$bytes = wasm_read_bytes('my_program.wasm');
$instance = wasm_new_instance($bytes);
```

This function returns a resource of type `wasm_instance`.


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
