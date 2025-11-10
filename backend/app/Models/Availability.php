<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'availability_submission_id', 'work_date', 'lunch', 'dinner',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lunch' => 'boolean',
            'dinner' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<AvailabilitySubmission, $this>
     */
    public function availabilitySubmission(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySubmission::class);
    }
}
