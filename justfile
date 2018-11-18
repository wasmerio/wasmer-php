compile-toy:
	#!/usr/bin/env bash
	set -euo pipefail
	cd tests
	rustc --target wasm32-unknown-unknown  -O --crate-type=cdylib toy.rs -o toy.raw.wasm
	wasm-gc toy.raw.wasm toy.wasm
	rm toy.raw.wasm

rust:
	cargo build --release

php:
	#!/usr/bin/env bash
	set -euo pipefail
	cd extension
	PHP_PREFIX_BIN=$(php-config --prefix)/bin
	$PHP_PREFIX_BIN/phpize --clean
	$PHP_PREFIX_BIN/phpize
	./configure --with-php-config=$PHP_PREFIX_BIN/php-config
	make install

# Local Variables:
# mode: makefile
# End:
