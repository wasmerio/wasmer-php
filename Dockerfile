# docker env for php-ext-wasm
## reference
# * https://circleci.com/gh/wasmerio/php-ext-wasm

FROM circleci/php:latest

RUN git clone https://github.com/wasmerio/php-ext-wasm.git $HOME/project \
    && cd $HOME/project && git checkout trying && cd $HOME \
    && test -d /usr/local/cargo || curl https://sh.rustup.rs -sSf | sh -s -- -y \
    && export PATH="$HOME/.cargo/bin:$PATH" \
    && test -f $HOME/.cargo/bin/just || cargo install just \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar composer \
    && export PATH="$HOME/.cargo/bin:$PATH" \
    && export CXX="gcc" \
    && EXTENSION="$HOME/project/src" \
    && cd $EXTENSION \
    && PHP_PREFIX=$(php-config --prefix) \
    && PHP_PREFIX_BIN=$PHP_PREFIX/bin \
    && $PHP_PREFIX_BIN/phpize --clean \
    && $PHP_PREFIX_BIN/phpize \
    && ./configure --with-php-config=$PHP_PREFIX_BIN/php-config \
    && /bin/bash $EXTENSION/libtool --mode=compile $CXX -I. -I$EXTENSION -DPHP_ATOM_INC -I$EXTENSION/include -I$EXTENSION/main -I$EXTENSION -I$PHP_PREFIX/include/php -I$PHP_PREFIX/include/php/main -I$PHP_PREFIX/include/php/TSRM -I$PHP_PREFIX/include/php/Zend -I$PHP_PREFIX/include/php/ext -I$PHP_PREFIX/include/php/ext/date/lib -DHAVE_CONFIG_H -c $EXTENSION/wasm.cc -o wasm.lo -fPIC \
    && $CXX -I. -I$EXTENSION -DPHP_ATOM_INC -I$EXTENSION/include -I$EXTENSION/main -I$EXTENSION -I$PHP_PREFIX/include/php -I$PHP_PREFIX/include/php/main -I$PHP_PREFIX/include/php/TSRM -I$PHP_PREFIX/include/php/Zend -I$PHP_PREFIX/include/php/ext -I$PHP_PREFIX/include/php/ext/date/lib -DHAVE_CONFIG_H -c $EXTENSION/wasm.cc  -DPIC -o .libs/wasm.o -fPIC \
    && /bin/bash $EXTENSION/libtool --mode=link cc -DPHP_ATOM_INC -I$EXTENSION/include -I$EXTENSION/main -I$EXTENSION -I$PHP_PREFIX/include/php -I$PHP_PREFIX/include/php/main -I$PHP_PREFIX/include/php/TSRM -I$PHP_PREFIX/include/php/Zend -I$PHP_PREFIX/include/php/ext -I$PHP_PREFIX/include/php/ext/date/lib  -DHAVE_CONFIG_H  -g -O2    -o wasm.la -export-dynamic -avoid-version -prefer-pic -module -rpath $EXTENSION/modules  wasm.lo -Wl,-rpath,$EXTENSION/. -L$EXTENSION/. -lwasmer_runtime_c_api -fPIC \
    && cc -shared  .libs/wasm.o  -L$EXTENSION/. -lwasmer_runtime_c_api  -Wl,-rpath -Wl,$EXTENSION/. -Wl,-soname -Wl,wasm.so -o .libs/wasm.so -fPIC \
    && sudo make install-modules \
    && export PATH="$HOME/.cargo/bin:$PATH" \
    && cd $HOME/project \
    && composer config repo.packagist composer https://mirrors.aliyun.com/composer/ && composer install --no-progress \
    && cd /usr/local/etc/php/conf.d && sudo touch docker-php-ext-wasm.ini && sudo chmod 777 docker-php-ext-wasm.ini \
    && echo 'extension=wasm.so' > docker-php-ext-wasm.ini

CMD ["/bin/sh"]
