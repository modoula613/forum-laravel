<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeStrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = (string) $value;

        if (mb_strlen($password) < 8) {
            $fail('Le mot de passe doit contenir au moins 8 caracteres.');
        }

        if (! preg_match('/[a-z]/', $password)) {
            $fail('Le mot de passe doit contenir au moins une lettre minuscule.');
        }

        if (! preg_match('/[A-Z]/', $password)) {
            $fail('Le mot de passe doit contenir au moins une lettre majuscule.');
        }

        if (! preg_match('/\d/', $password)) {
            $fail('Le mot de passe doit contenir au moins un chiffre.');
        }

        if (! preg_match('/[^a-zA-Z0-9]/', $password)) {
            $fail('Le mot de passe doit contenir au moins un caractere special.');
        }

        if (preg_match('/<\?(?:php)?|<script\b|<\/script>|[<>]/i', $password)) {
            $fail('Le mot de passe contient des sequences interdites.');
        }
    }
}
