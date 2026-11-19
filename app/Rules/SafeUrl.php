<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function __construct(private readonly bool $absolute = false, private readonly bool $httpsOnly = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $url = is_string($value) ? safe_url($value) : null;
        if ($this->httpsOnly) {
            if ($url === null || ! str_starts_with(strtolower($url), 'https://')) {
                $fail('Nhập địa chỉ web bắt đầu bằng https://.');
            }

            return;
        }
        if ($url === null || ($this->absolute && ! preg_match('~^https?://~i', $url))) {
            $fail($this->absolute
                ? 'Nhập địa chỉ web bắt đầu bằng http:// hoặc https://.'
                : 'Nhập địa chỉ web, đường dẫn bắt đầu bằng / hoặc liên kết bắt đầu bằng #.');
        }
    }
}
