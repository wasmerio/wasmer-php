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
	PHP_PREFIX_BIN=$(php-config --prefix)/bin
	$PHP_PREFIX_BIN/phpize --clean
	$PHP_PREFIX_BIN/phpize
	./configure --with-php-config=$PHP_PREFIX_BIN/php-config
	make install

# Run PHP tests.
test-php:
	composer test

# Local Variables:
# mode: makefile
# End:
