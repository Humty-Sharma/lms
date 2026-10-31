<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, [UserRole::Admin, UserRole::Member], true);
    }

    public function rules(): array
    {
        return [
            'original_url' => [
                'required',
                'string',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $scheme = parse_url($value, PHP_URL_SCHEME);
                    if (! in_array(strtolower((string) $scheme), ['http', 'https'], true)) {
                        $fail('The destination URL must start with http:// or https://.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Please provide a valid destination URL.',
            'original_url.url' => 'The destination URL must be a valid URL.',
        ];
    }
}

