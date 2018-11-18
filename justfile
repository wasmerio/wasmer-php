compile-toy:
	cd tests && \
		rustc --target wasm32-unknown-unknown  -O --crate-type=cdylib toy.rs -o toy.raw.wasm && \
		wasm-gc toy.raw.wasm toy.wasm && \
		rm toy.raw.wasm

php:
	cd extension && \
		PHP_PREFIX_BIN=$(php-config --prefix)/bin && \
		$PHP_PREFIX_BIN/phpize --clean && \
		$PHP_PREFIX_BIN/phpize && \
		./configure --with-php-config=$PHP_PREFIX_BIN/php-config && \
		make install

c:
	clang \
		-Wall \
		-o test-c \
		test.c \
		-L target/release/ \
		-l php_ext_wasm \
		-l System \
		-l pthread \
		-l c \
		-l m

# Local Variables:
# mode: makefile
# End:
