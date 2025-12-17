<?php

namespace App\Http\Requests\Availability;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'special_requests' => 'nullable|string|max:1000',
            'availabilities' => 'required|array|min:1',
            'availabilities.*.work_date' => 'required|date',
            'availabilities.*.lunch' => 'required|boolean',
            'availabilities.*.dinner' => 'required|boolean',
        ];
    }
}
