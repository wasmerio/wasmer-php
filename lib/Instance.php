<?php

declare(strict_types = 1);

namespace WASM;

use Closure;
use ReflectionObject;
use RuntimeException;

final class Instance
{
    private $filePath;
    private $wasmRuntime;
    private $wasmBinary;
    private $importedFunctions = [];

    public function __construct(string $filePath, array $importedFunctions = [])
    {
        if (false === file_exists($filePath)) {
            throw new RuntimeException("File path to WASM binary `$filePath` does not exist.");
        }

        $this->buildImportedFunctions($importedFunctions);

        $this->filePath = $filePath;
        $this->wasmBinary = wasm_read_binary($this->filePath);
        $this->wasmRuntime = wasm_new_runtime();

        foreach ($this->importedFunctions as $importedFunctionName => $importedFunction) {
            wasm_runtime_add_function(
                $this->wasmRuntime,
                $importedFunction['index'],
                $importedFunctionName,
                $importedFunction['signature'],
                $importedFunction['implementation']
            );
        }

        $this->wasmInstance = wasm_new_instance(
            $this->filePath,
            $this->wasmBinary,
            $this->wasmRuntime
        );

        if (null === $this->wasmInstance) {
            throw new RuntimeException("An error happened while instanciating the module `$filePath`.");
        }
    }

    private function buildImportedFunctions(array $importedFunctions) {
        $index = 0;

        foreach ($importedFunctions as $functionName => $functionImplementation) {
            if (!($functionImplementation instanceof Closure)) {
                throw new RuntimeException("Import function `$functionName` must be a closure.");
            }

            $reflection = new ReflectionObject($functionImplementation);
            $invoke = $reflection->getMethod('__invoke');
            $inputs = $invoke->getParameters();

            $signature = [];

            foreach ($inputs as $input) {
                if (true !== $input->hasType()) {
                    throw new RuntimeException(
                        "Argument `\${$input->getName()}` " .
                        "of the imported function `$functionName` must have a type."
                    );
                }

                $type = $input->getType();

                if (true === $type->allowsNull()) {
                    throw new RuntimeException(
                        "Argument `\${$input->getName()}` " .
                        "of the imported function `$functionName` must _not_ have a nullable type."
                    );
                }

                switch ($type . '') {
                    case 'int':
                    case 'double':
                        $signature[] = SIGNATURE_TYPE_I32;
                        break;

                    case 'float':
                        $signature[] = SIGNATURE_TYPE_F32;
                        break;

                    default:
                        throw new RuntimeException(
                            "Argument `\${$input->getName()}` " .
                            "of the imported function `$functionName` has an invalid type: " .
                            "Only `int`, `double`, or `float` is supported."
                        );
                }
            }

            $output = $invoke->getReturnType();

            if (null === $output) {
                throw new RuntimeException(
                    "The imported function `$functionName` must have a return type."
                );
            }

            if (true === $output->allowsNull()) {
                throw new RuntimeException(
                    "The imported function `$functionName` must _not_ have a nullable return type."
                );
            }

            switch ($output . '') {
                case 'int':
                case 'double':
                    $signature[] = SIGNATURE_TYPE_I32;
                    break;

                case 'float':
                    $signature[] = SIGNATURE_TYPE_F32;
                    break;

                default:
                    throw new RuntimeException(
                        "The imported function `$functionName` has an invalid return type: " .
                        "Only `int`, `double`, or `float` is supported."
                    );
            }

            $this->importedFunctions[$functionName] = [
                'index' => $index++,
                'signature' => $signature,
                'implementation' => function (...$arguments) use ($functionImplementation) {
                    return $functionImplementation(...$arguments);
                }
            ];
        }
    }

    public function __call(string $name, array $arguments)
    {
        $signature = wasm_get_function_signature($this->wasmInstance, $name);

        if (null === $signature) {
            throw new InvocationException("Function `$name` does not exist.");
        }

        $number_of_expected_arguments = count($signature) - 1;
        $number_of_given_arguments = count($arguments);
        $diff = $number_of_expected_arguments - $number_of_given_arguments;

        if ($diff > 0) {
            throw new InvocationException(
                "Missing $diff argument(s) when calling `$name`: " .
                "Expect $number_of_expected_arguments arguments, " .
                "given $number_of_given_arguments."
            );
        } elseif ($diff < 0) {
            $diff = abs($diff);

            throw new InvocationException(
                "Given $diff extra argument(s) when calling `$name`: " .
                "Expect $number_of_expected_arguments arguments, " .
                "given $number_of_given_arguments."
            );
        }

        $argumentsBuilder = new ArgumentsBuilder();

        foreach ($arguments as $i => $argument) {
            $s = $i + 1;

            switch ($signature[$i]) {
                case SIGNATURE_TYPE_I32:
                    if (!is_int($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `i32` (integer).");
                    }

                    $argumentsBuilder->addI32($argument);

                    break;

                case SIGNATURE_TYPE_I64:
                    if (!is_int($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `i64` (integer).");
                    }

                    $argumentsBuilder->addI64($argument);

                    break;

                case SIGNATURE_TYPE_F32:
                    if (!is_float($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `f32` (float).");
                    }

                    $argumentsBuilder->addF32($argument);

                    break;

                case SIGNATURE_TYPE_F64:
                    if (!is_float($argument)) {
                        throw new InvocationException("Argument #$s of `$name` must be a `f64` (float).");
                    }

                    $argumentsBuilder->addF64($argument);

                    break;

                default:
                    throw new InvocationException("Unknown argument type `$signature[$i]` at position #$s of `$name`.");
            }
        }

        $argumentsBuilderResource = $argumentsBuilder->intoResource();
        $result = wasm_invoke_function(
            $this->wasmInstance,
            $name,
            $argumentsBuilderResource,
            $this->wasmRuntime
        );

        if (false === $result) {
            throw new InvocationException("Got an error when invoking `$name`.");
        }

        return $result;
    }
}

final class InvocationException extends RuntimeException {}
