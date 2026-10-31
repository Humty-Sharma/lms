<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
        ];

        if ($user?->isSuperAdmin()) {
            $rules['company_name'] = ['required', 'string', 'min:2', 'max:191'];
        } elseif ($user?->isAdmin()) {
            $rules['role'] = [
                'required',
                'string',
                Rule::in(UserRole::assignableByAdmin()),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email address already exists in the system.',
            'company_name.required' => 'A company name is required when creating a new company invitation.',
            'role.in' => 'You may only invite users as an Admin or Member.',
        ];
    }
}

