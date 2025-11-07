<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'availability_submission_id', 'work_date', 'lunch', 'dinner',
    ];

    public function availabilitySubmission(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySubmission::class);
    }
}
