# 🐘+🦀+🕸️ = PHP `ext-wasm`

_This is only experimental right now_.

The goal of the project is to be able to run WebAssembly binaries from
PHP directly. So much fun coming!

## What is WebAssembly?

Quoting [the WebAssembly site](https://webassembly.org/):

> WebAssembly (abbreviated WASM) is a binary instruction format for a
> stack-based virtual machine. WASM is designed as a portable target
> for compilation of high-level languages like C/C++/Rust, enabling
> deployment on the web for client and server applications.

About speed:

> WebAssembly aims to execute at native speed by taking advantage of
> [common hardware
> capabilities](https://webassembly.org/docs/portability/#assumptions-for-efficient-execution)
> available on a wide range of platforms.

About safety:

> WebAssembly describes a memory-safe, sandboxed [execution
> environment](https://webassembly.org/docs/semantics/#linear-memory) […].

## Goals

This extension has some goals in minds. Let's list some of them:

### Write PHP extensions

Taking the example of an image manipulation library, like face
detection, one can use an existing Rust or C++ library, then compile
it to a WebAssembly binary, and use it directly in PHP through the
`php-ext-wasm` extension.

Writing a C extension for PHP with the Zend API is no longer necessary.

### Cross-platform distribution and packaging (feat. Composer)

Because WebAssembly is a portable target, i.e. binaries are platform
agnostics, once a library has been compiled to a WebAssembly binary,
it is immediately distributable on all platforms where PHP runs. There
is no compilation steps required. And to be clear, compiling a library
to a WebAssembly binary does not required any PHP headers.

To push the logic further, a library compiled as a WebAssembly binary
can be packaged with [Composer](https://getcomposer.org/) (the PHP
dependency manager), along with some PHP code to ease the manipulation
of the compiled library.

Distributing a new version of a WebAssembly binary simply reduces to
distributing a new file. Composer can also add [constraints over the
available extensions with their
versions](https://getcomposer.org/doc/04-schema.md#package-links) (see
<code>ext-<em>name</em></code>). All packaging scenarios are handled.

### As fast as possible

Right now, `php-ext-wasm` uses [the wasmi
library](https://github.com/paritytech/wasmi). No benchmark has been
run yet, but we know this is not the fastest WebAssembly interpreter:
It is safe, it is solid, it is well-tested, it provides a neat and
flexible API, and this is what we need for the first version of this
project.

It is expected to add
[Cranelift](https://github.com/CraneStation/cranelift), and its
[Just-In-Time library](https://crates.io/crates/cranelift-simplejit),
at some point in the future. We are already experimenting with it. The
hope is to reach similar performance than PHP extensions written in C,
or to be very close to it.

### Safety first

WebAssembly brings safety guarantees, notably due to its memory model
and its sandboxed [execution
environment](https://webassembly.org/docs/semantics/#linear-memory). If
Rust is used as the source of a WebAssembly binary, then more safety
is brought in the game. In any case, without real numbers or studies,
we imagine that it is safer to use a WebAssembly binary extension
rather than writing C code.

A WebAssembly binary has no access to the PHP environment (so no
access to its memory or functions). It is executed in a sandbox. A
WebAssembly binary is totally blind regarding the host/the system
where it runs: Whether it runs in a Web browser, a server, or a PHP
process, this is the same.

## Examples

### Toy example

There is a toy program in `examples/simple.rs`, written in Rust
(or any other language that compiles to WASM):

```rust
#[no_mangle]
pub extern fn sum(x: i32, y: i32) -> i32 {
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
pub extern fn sum(x: i32, y: i32) -> i32 {
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

1. The `src/` directory contains a Rust library that exposes an API to
   instantiate a WASM binary and invoke functions on it. It relies on
   [the `wasmi` library](https://github.com/paritytech/wasmi). The
   `src/` directory also exposes C bindings (FFI).

2. The `extension/` directory contains a PHP extension, written in
   C. This extension binds and exposes the C API from the Rust library
   to Zend Engine (the PHP VM). Few low-level functions are exposed,
   such as `wasm_read_binary`, `wasm_new_runtime`,
   `wasm_new_instance`, `wasm_invoke_arguments_builder`,
   `wasm_invoke_function` etc. See `php -d extension=wasm --re wasm`
   to get a full list.

3. Then, the `lib/` directory contains a more user-friendly PHP API
   built upon the API provided by the PHP extension, with class like
   `WASM\Instance`.

4. The `headers/` directory contains headers generated by the Rust
   program automatically. These headers are used by the C PHP
   extension.

5. The `tests/` directory contains PHP tests. To run them: `composer
   install && composer test`.

6. Misc: The `examples/` directory contains… examples!

To compile the entire project, run the following commands:

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
