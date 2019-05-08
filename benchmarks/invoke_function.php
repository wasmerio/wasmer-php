<?php

declare(strict_types = 1);

/**
 * @BeforeMethods({"initialize"})
 * @Warmup(2)
 * @Revs(1000)
 * @Iterations(10)
 * @OutputTimeUnit("microseconds", precision=3)
 * @OutputMode("time")
 */
class InvokeFunction
{
    private $wasmInstance = null;

    public function initialize(array $parameters = [])
    {
        $this->wasmInstance = new Wasm\Instance(__DIR__ . '/../tests/units/tests.wasm');
    }

    public function bench_invoke_sum()
    {
        return $this->wasmInstance->sum(1, 2);
    }

    public function bench_invoke_i32_i64_f32_f64_f64()
    {
        return $this->wasmInstance->i32_i64_f32_f64_f64(1, 2, 3., 4.);
    }

}
