PHP_ARG_ENABLE(wasm, whether to enable wasm support,
[  --enable-wasm          Enable wasm support], no)

if test "$PHP_WASM" != "no"; then
  AC_DEFINE(HAVE_WASM, 1, [ Have wasm support ])

  PHP_SUBST(WASM_SHARED_LIBADD)
  PHP_ADD_LIBRARY_WITH_PATH(wasmer_runtime_c_api, $ac_pwd, WASM_SHARED_LIBADD)

  PHP_NEW_EXTENSION(wasm, wasm.cc, $ext_shared)
fi
