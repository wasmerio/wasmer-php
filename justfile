compile-wasm FILE='examples/simple':
	#!/usr/bin/env bash
	set -euo pipefail
	rustc --target wasm32-unknown-unknown -O --crate-type=cdylib {{FILE}}.rs -o {{FILE}}.raw.wasm
	wasm-gc {{FILE}}.raw.wasm {{FILE}}.wasm
	rm {{FILE}}.raw.wasm

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

test-php:
	composer test

# Local Variables:
# mode: makefile
# End:
