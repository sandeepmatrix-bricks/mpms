<?php

namespace App\Concerns;

use Closure;

/**
 * Helpers for editing JSON columns via plain textareas in the admin forms.
 */
trait HandlesJsonFields
{
    /** Decode a JSON textarea value into an array (empty input => []). */
    protected function decodeJson(?string $value): array
    {
        if (blank($value)) {
            return [];
        }

        return json_decode($value, true) ?? [];
    }

    /** A validation rule that fails when a non-empty value is not valid JSON. */
    protected function jsonRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (filled($value) && json_decode($value) === null && strtolower(trim($value)) !== 'null') {
                $fail("The {$attribute} field must contain valid JSON.");
            }
        };
    }
}
