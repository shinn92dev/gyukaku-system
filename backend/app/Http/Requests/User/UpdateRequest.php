<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|min:3|max:30|unique:users,username',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'role_ids.required' => 'Please include at least one role.',
        ];
    }
}
