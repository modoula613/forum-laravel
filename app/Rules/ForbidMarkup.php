<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ForbidMarkup implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $content = (string) $value;

        if (! preg_match('/<\?(?:php)?|<script\b|<\/script>|[<>]/i', $content)) {
            return;
        }

        $fail('Les balises HTML, scripts et tags PHP ne sont pas autorises.');
    }
}
