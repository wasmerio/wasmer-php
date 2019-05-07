# Compile a Rust program to Wasm.
compile-wasm FILE='examples/simple':
	#!/usr/bin/env bash
	set -euo pipefail
	rustc --target wasm32-unknown-unknown -O --crate-type=cdylib {{FILE}}.rs -o {{FILE}}.raw.wasm
	wasm-gc {{FILE}}.raw.wasm {{FILE}}.wasm
	wasm-opt -Os --strip-producers {{FILE}}.wasm -o {{FILE}}.opt.wasm
	mv {{FILE}}.opt.wasm {{FILE}}.wasm
	rm {{FILE}}.raw.wasm

# Compile the Rust part.
rust:
	cargo build --release

# Compile the PHP part.
php:
	#!/usr/bin/env bash
	set -euo pipefail
	cd extension
	test -f libwasmer_runtime_c_api.a && rm libwasmer_runtime_c_api.a
	ln -s ../target/release/deps/libwasmer_runtime_c_api-*.a libwasmer_runtime_c_api.a
	PHP_PREFIX_BIN=$(php-config --prefix)/bin
	$PHP_PREFIX_BIN/phpize --clean
	$PHP_PREFIX_BIN/phpize
	export CXX='g++'
	export CXXFLAGS='-std=c++11'
	./configure --with-php-config=$PHP_PREFIX_BIN/php-config
	make install

# Run PHP tests.
test:
	#!/usr/bin/env bash
	PHP_PREFIX_BIN=$(php-config --prefix)/bin
	$PHP_PREFIX_BIN/php $(which composer) test

# Run PHP benchmarks.
bench:
	#!/usr/bin/env bash
	PHP_PREFIX_BIN=$(php-config --prefix)/bin
	$PHP_PREFIX_BIN/php $(which composer) bench

# Generate the documentation.
doc:
	composer doc

# Local Variables:
# mode: makefile
# End:
