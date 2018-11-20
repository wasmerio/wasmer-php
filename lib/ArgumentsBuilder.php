<?php

declare(strict_types = 1);

namespace WASM;

final class ArgumentsBuilder
{
    private $builder;

    public function __construct()
    {
        $this->builder = wasm_invoke_arguments_builder();
    }

    public function addI32(int $i32): self
    {
        wasm_invoke_arguments_builder_add_i32($this->builder, $i32);

        return $this;
    }

    public function addI64(int $i64): self
    {
        wasm_invoke_arguments_builder_add_i64($this->builder, $i64);

        return $this;
    }

    public function addF32(float $f32): self
    {
        wasm_invoke_arguments_builder_add_f32($this->builder, $f32);

        return $this;
    }

    public function addF64(float $f64): self
    {
        wasm_invoke_arguments_builder_add_f64($this->builder, $f64);

        return $this;
    }

    public function intoResource()
    {
        return $this->builder;
    }
}
