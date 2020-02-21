<p align="center">
  <a href="https://wasmer.io" target="_blank" rel="noopener">
    <img width="300" src="https://raw.githubusercontent.com/wasmerio/wasmer/master/assets/logo.png" alt="Wasmer logo">
  </a>
</p>

<p align="center">
  <a href="https://spectrum.chat/wasmer">
    <img src="https://withspectrum.github.io/badge/badge.svg" alt="Join the Wasmer Community">
  </a>
  <a href="https://wasmerio.github.io/php-ext-wasm/wasm/">
    <img src="https://img.shields.io/badge/documentation-API-ff0066.svg" alt="Read our API documentation">
  </a>
  <a href="https://packagist.org/packages/php-wasm/php-wasm">
      <img src="https://img.shields.io/packagist/dt/php-wasm/php-wasm.svg" alt="Packagist" />
  </a>
  <a href="https://github.com/wasmerio/wasmer/blob/master/LICENSE">
    <img src="https://img.shields.io/github/license/wasmerio/wasmer.svg" alt="License">
  </a>
</p>

# The PHP extension to run WebAssembly

The goal of the project is to be able to run WebAssembly binaries from
PHP directly. So much fun coming!

## What is WebAssembly?

Quoting [the WebAssembly site](https://webassembly.org/):

> WebAssembly (abbreviated Wasm) is a binary instruction format for a
> stack-based virtual machine. Wasm is designed as a portable target
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

We are working on being as fast as native code (see [this blog post to
learn more][wasmi-to-wasmer]). So far, the extension provides a faster
execution than PHP itself. With the `nbody` benchmark, the
`php-ext-wasm` is 9.5 times faster than pure PHP:

| subject | mean | mode | best | rstdev |
|--|-:|-:|-:|-:|
| `wasm_extension` | 2,009.335μs | 1,991.778μs | 1,968.595μs | 2.17% |
| `pure_php` | 19,714.738μs | 19,143.083μs | 18,853.399μs | 3.58% |


[wasmi-to-wasmer]: https://medium.com/wasmer/php-ext-wasm-migrating-from-wasmi-to-wasmer-4d1014f41c88

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

## Example

There is a toy program in `examples/simple.rs`, written in Rust
(or any other language that compiles to Wasm):

```rust
#[no_mangle]
pub extern fn sum(x: i32, y: i32) -> i32 {
    x + y
}
```

This program compiles to Wasm, with `just compile-wasm
examples/simple`. We end up with a `examples/simple.wasm` binary file.

Then, we can execute it in PHP (!) with the `examples/simple.php` file:

```php
$instance = new Wasm\Instance(__DIR__ . '/simple.wasm');

var_dump(
    $instance->sum(5, 37) // 42!
);
```

And then, finally, enjoy by running:

```sh
$ php -d extension=wasm examples/simple.php
int(42)
```

## Usage

This repository contains basically two things:

1. The `php-ext-wasm` extension, and
2. The `Wasm` library.
  
The `php-ext-wasm` extension provides a raw API around
WebAssembly. The `Wasm` library is a layer on top of `php-ext-wasm` to
provide more safety and a more user-friendly API.

See the [API documentations with examples](https://wasmerio.github.io/php-ext-wasm/wasm/).

### Install with PECL (recommended)

PECL is the official repository for PHP extensions. This project is
hosted on PECL under the name `wasm`: http://pecl.php.net/wasm.

```sh
$ pecl install -B wasm
$ cd /tmp/pear/temp/wasm/src
$ phpize
$ export CXX='g++'
$ export CXXFLAGS='-std=c++11'
$ ./configure --with-php-config=$PHP_PREFIX_BIN/php-config
$ make install
```

This process will be easier in a close future.

### Install manually (development)

To compile the entire project, run the following commands:

```sh
$ just build
$ php -d extension=wasm examples/simple.php
```

If the provided shared libraries are not compatible with your system,
please try running `just build-runtime` first.

(Yes, you need [`just`](https://github.com/casey/just/)).

## Testing

Once the extension is compiled and installed (just run `just rust && just php`), run the following commands:

```sh
$ composer install
$ composer test
```

## License

The entire project is under the MIT License. Please read [the
`LICENSE`
file](https://github.com/wasmerio/wasmer/blob/master/LICENSE).
