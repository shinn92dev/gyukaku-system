<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'work_date' => 'required|date',
            'type' => 'required|string|in:foh,boh,mgr',
            'is_understaffed' => 'sometimes|boolean',
            'shifts' => 'required|array|min:1',
            'shifts.*.user_id' => 'required|integer|exists:users,id',
            'shifts.*.work_date' => 'required|date',
            'shifts.*.shift_start_time' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
