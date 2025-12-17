<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'work_date' => 'sometimes|date',
            'type' => 'sometimes|string|in:foh,boh,mgr',
            'is_understaffed' => 'sometimes|boolean',
            'shifts' => 'sometimes|array|min:1',
            'shifts.*.id' => 'required|integer|exists:shifts,id',
            'shifts.*.user_id' => 'sometimes|integer|exists:users,id',
            'shifts.*.shift_start_time' => 'sometimes|date_format:Y-m-d H:i:s',
            'shifts.*.work_date' => 'sometimes|date',
        ];
    }
}
