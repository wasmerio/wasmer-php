# Module caching

Why would one like to cache WebAssembly modules? A particular usecase
is the following: A WebAssembly module can be compiled ahead-of-time
and stored in a cache, thus saving further compilations.

So far, only an on-disk cache exists with `Wasm\Cache\Filesystem`.

All cache implementations must implement the
`Wasm\Cache\CacheInterface` interface. It relies on the
[PSR-16](https://www.php-fig.org/psr/psr-16/) specification for more
simplicity and interoperability.

## Modules as bytes, and _vice versa_

A `Wasm\Module` object can be serialized into a sequence of bytes. To
serialize a module, use [the native `serialize` PHP
function](http://php.net/serialize), such as:

```php,ignore
$module = new Wasm\Module('my_program.wasm');
$serialized_module = serialize($module);
```

To deserialize a sequence of bytes to a module, use [the native
`unserialize` PHP function](http://php.net/unserialize).

**Security note**: The second argument of `unserialize` must be used
to specify allowed classes to be deserialized, in this case
`Wasm\Module`.

Let's see:

```php
$module = unserialize($serialized_module, [Wasm\Module::class]);
```

