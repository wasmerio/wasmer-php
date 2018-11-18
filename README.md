# PHP `ext-wasm`

This is only experimental right now.

The goal of the project is to be able to run WebAssembly binaries from
PHP directly. So much fun coming!

## Examples

There is a toy program in `tests/`, written en Rust:

```rust
#[no_mangle]
pub extern "C" fn sum(x: i32, y: i32) -> i32 {
    x + y
}
```

This program compiles to WASM, with `just compile-toy`. We end up with
a `tests/toy.wasm` binary file.

Then, we can read it with the `tests/toy.php` file:

```php
<?php

require_once dirname(__DIR__) . '/lib/WASM.php';

$instance = new WASM\Instance(__DIR__ . '/toy.wasm');
$instance->sum(5, 37);
```

And then, finally, enjoy by running:

```sh
$ php -d extension=wasm tests/toy.php
Some(I32(42))
```

This is a very preliminary status, but it works!

## The whole schema

The `src/` directory contains a Rust library that exposes an API to
instanciate a WASM binary and invoke functions on it. It relies on
[the `wasmi` library](https://github.com/paritytech/wasmi). The `src/`
directory also exposes a C binding.

The `extension/` directory contains a PHP extension, written in C, and
exposing the C API from the Rust library to Zend Engine (the PHP
VM). Few low-level functions are exposed, such as `wasm_read_binary`,
`wasm_new_instance`, `wasm_invoke_arguments_builder`,
`wasm_invoke_function` etc.

Then, the `lib/` directory contains a more user-friendly PHP API built
upon the API provided by the PHP extension, with class like
`WASM\Instance`.

To compile the entire thing, run the following commands:

```sh
$ just rust
$ just php
$ php -d extension=wasm tests/toy.php
```

(Yes, you need [`just`](https://github.com/casey/just/)).

## License

The entire project is under the BSD-3-Clause license. Please read the
`LICENSE` file.
