<?php

namespace App\Http\Requests\Availability;

use App\Models\AvailabilitySubmission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->user()->is_admin) {
            return true;
        }

        /** @var AvailabilitySubmission $submission */
        $submission = AvailabilitySubmission::findOrFail($this->route('id'));

        return $submission->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var AvailabilitySubmission|null $submission */
        $submission = AvailabilitySubmission::find($this->route('id'));

        return [
            'start_date' => [
                'sometimes',
                'date',
                function ($attribute, $value, $fail) use ($submission) {
                    $endDate = $this->input('end_date', $submission?->end_date);
                    if ($endDate && $value >= $endDate) {
                        $fail('The start date must be before the end date.');
                    }
                },
            ],
            'end_date' => [
                'sometimes',
                'date',
                function ($attribute, $value, $fail) use ($submission) {
                    $startDate = $this->input('start_date', $submission?->start_date);
                    if ($startDate && $value <= $startDate) {
                        $fail('The end date must be after the start date.');
                    }
                },
            ],
            'special_requests' => 'sometimes|nullable|string|max:1000',

            // Availabilities array
            'availabilities' => 'sometimes|array',
            'availabilities.*.id' => 'required|integer|exists:availabilities,id',
            'availabilities.*.lunch' => 'sometimes|boolean',
            'availabilities.*.dinner' => 'sometimes|boolean',
        ];
    }
}
