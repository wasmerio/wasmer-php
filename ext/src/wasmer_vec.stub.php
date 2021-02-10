<?php

/** @generate-function-entries */

namespace Wasm\Vec;

///////////////////////////////////////////////////////////////////////////////
// Type Representations

final class ExportType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrExporttypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class ExternType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrExterntypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class FuncType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrFunctypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class GlobalType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrGlobaltypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class ImportType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrImporttypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class MemoryType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrMemorytypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class TableType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrTabletypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class ValType implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrValtypes = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

///////////////////////////////////////////////////////////////////////////////
// Runtime Objects

final class Extern implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrExterns = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class Frame implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrFrames = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}

final class Val implements \Countable, \ArrayAccess {
    public function __construct(array|int|null $sizeOrVals = null) {}
    public function count(): int {}
    public function offsetExists(mixed $offset): bool {}
    /**
     * @throw \Wasm\Exception\OutOfBoundsException
     *
     * @return resource
     */
    public function offsetGet(mixed $offset): mixed {}
    /**
     * @param resource $value
     *
     * @throw \Wasm\Exception\OutOfBoundsException
     */
    public function offsetSet(mixed $offset, mixed $value): void {}
    /** @throw \Exception */
    public function offsetUnset(mixed $offset): void {}
}
