--TEST--
ValType API classes

--FILE--
<?php

var_dump(
    class_exists('Wasm\\Vec\\ValType'),
);

?>
--EXPECTF--
bool(true)
