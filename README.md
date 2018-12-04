# 🐘+🦀+🕸️ = PHP `ext-wasm`

This is only experimental right now.

The goal of the project is to be able to run WebAssembly binaries from
PHP directly. So much fun coming!

## Examples

### Toy example

There is a toy program in `examples/simple.rs`, written in Rust
(or any other language that compiles to WASM):

```rust
#[no_mangle]
pub extern "C" fn sum(x: i32, y: i32) -> i32 {
    x + y
}
```

This program compiles to WASM, with `just compile-wasm
examples/simple`. We end up with a `examples/simple.wasm` binary file.

Then, we can execute it in PHP (!) with the `examples/simple.php` file:

```php
$instance = new WASM\Instance(__DIR__ . '/simple.wasm');

var_dump(
    $instance->sum(5, 37) // 42!
);
```

And then, finally, enjoy by running:

```sh
$ php -d extension=wasm examples/simple.php
int(42)
```

### Imported functions

There is another toy example called `examples/imported_function.rs`:

```rust
extern {
    fn add(x: i32, y: i32) -> i32;
}


#[no_mangle]
pub extern "C" fn sum(x: i32, y: i32) -> i32 {
    unsafe {
        add(x, y) + 1
    }
}
```

What happens here? The Rust program depends on an `add` function that
is defined outside itself. This is an extern function. The magic is
that this function implementation will be defined in PHP! The
`examples/imported_function.php` contains this:

```php
$imports = [
    'add' => function(int $x, int $y): int {
        return $x + $y + 1;
    },
];
$instance = new WASM\Instance(__DIR__ . '/imported_function.wasm', $imports);

var_dump(
    $instance->sum(5, 35) // 42
);
```

The `add` function is defined in PHP itself. This kind of function is
called an imported function.

The function must be annotated with types to infer the function
signature and check if it matches with the extern function.

And then, finally, get excited by running:

```sh
$ php -d extension=wasm examples/imported_function.php
int(42)
```

PHP calls `sum` (defined in Rust) with 5 and 35. The `sum` function
calls `add` (defined in PHP) with 5 and 35, and adds 1. The `add`
function adds 5 and 35, and adds 1. So 5 + 35 + 1 + 1 = 42. All
“environments” are called. Q.E.D.

Imported functions are very exciting. A program, written in Rust for
instance, can call functions from your favorite framework or
libraries. It increases the interoperability of your program.

## The whole schema

The `src/` directory contains a Rust library that exposes an API to
instantiate a WASM binary and invoke functions on it. It relies on
[the `wasmi` library](https://github.com/paritytech/wasmi). The `src/`
directory also exposes C (FFI) bindings.

The `extension/` directory contains a PHP extension, written in C, and
exposing the C API from the Rust library to Zend Engine (the PHP
VM). Few low-level functions are exposed, such as `wasm_read_binary`,
`wasm_new_runtime`, `wasm_new_instance`,
`wasm_invoke_arguments_builder`, `wasm_invoke_function` etc. See `php
-d extension=wasm --re wasm` to get a full list.

Then, the `lib/` directory contains a more user-friendly PHP API built
upon the API provided by the PHP extension, with class like
`WASM\Instance`.

To compile the entire thing, run the following commands:

```sh
$ just rust
$ just php
$ php -d extension=wasm examples/simple.php
```

(Yes, you need [`just`](https://github.com/casey/just/)).

## Planning

* [x] Read a WASM binary,
* [x] Instanciate a WASM binary,
* [x] Get function signatures,
* [x] Arguments builder,
* [x] Invoke function:
  * [x] with `i32` as arguments or returned value,
  * [x] with `i64` as arguments or returned value,
  * [x] with `f32` as arguments or returned value,
  * [x] with `f64` as arguments or returned value.
* [x] User-friendly PHP API above the low-level PHP extension API,
* [ ] Expose memory:
  * [ ] Array view for `i32`,
  * [ ] Array view for `i64`,
  * [ ] Array view for `f32`,
  * [ ] Array view for `f64`.
  * [ ] Readable array view,
  * [ ] Writable array view.
* [ ] Import functions:
  * [x] Specify signatures,
  * [ ] Support callable,
  * [x] Support closure,
  * [ ] Support named function.

## License

The entire project is under the BSD-3-Clause license. Please read the
`LICENSE` file.
