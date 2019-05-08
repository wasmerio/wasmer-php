<?php

declare(strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Instantiate the module.
$instance = new Wasm\Instance(__DIR__ . '/greet.wasm');

// Set the subject to greet.
$subject = 'Wasmer 🐘';
$length_of_subject = strlen($subject);

// Allocate memory for the subject, and get a pointer to it.
$input_pointer = $instance->allocate($length_of_subject);

// Write the subject into the memory.
$memory_buffer = $instance->getMemoryBuffer();
$memory = new Wasm\Uint8Array($memory_buffer, $input_pointer);

for ($nth = 0; $nth < $length_of_subject; ++$nth) {
    $memory[$nth] = ord($subject[$nth]);
}

// C-string terminates by NULL.
$memory[$nth] = 0;

// Run the `greet` function. Give the pointer to the subject.
$output_pointer = $instance->greet($input_pointer);

// Read the result of the `greet` function.
$memory = new Wasm\Uint8Array($memory_buffer, $output_pointer);

$output = '';
$nth = 0;

while (0 !== $memory[$nth]) {
    $output .= chr($memory[$nth]);
    ++$nth;
}

$length_of_output = $nth;

echo $output, "\n";

// Deallocate the subject, and the output.
$instance->deallocate($input_pointer, $length_of_subject);
$instance->deallocate($output_pointer, $length_of_output);
