# Compile a Rust program to Wasm.
compile-wasm FILE='examples/simple':
	#!/usr/bin/env bash
	set -euo pipefail
	rustc --target wasm32-unknown-unknown -O --crate-type=cdylib {{FILE}}.rs -o {{FILE}}.raw.wasm
	wasm-gc {{FILE}}.raw.wasm {{FILE}}.wasm
	wasm-opt -Os --strip-producers {{FILE}}.wasm -o {{FILE}}.opt.wasm
	mv {{FILE}}.opt.wasm {{FILE}}.wasm
	rm {{FILE}}.raw.wasm

# Build the runtime shared library for this specific system.
build-runtime:
	#!/usr/bin/env bash
	set -euo pipefail

	# Build the shared library.
	cargo build --release

	# Find the shared library extension.
	case "{{os()}}" in
		"macos")
			shared_library_path=$( ls -t target/release/deps/libwasmer_runtime_c_api*.dylib | head -n 1 )
			shared_library=libwasmer_runtime_c_api.dylib
			;;
		"windows")
			shared_library_path=$( ls -t target/release/deps/wasmer_runtime_c_api*.dll | head -n 1 )
			shared_library=wasmer_runtime_c_api.dll
			;;
		*)
			shared_library_path=$( ls -t target/release/deps/libwasmer_runtime_c_api*.so | head -n 1 )
			shared_library=libwasmer_runtime_c_api.so
	esac

	# Link `wasmer/*wasmer_runtime_c_api.*`.
	rm -f wasmer/${shared_library}
	ln -s "../${shared_library_path}" wasmer/${shared_library}

# Compile the PHP extension.
build:
	#!/usr/bin/env bash
	set -euo pipefail
	cd src
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
