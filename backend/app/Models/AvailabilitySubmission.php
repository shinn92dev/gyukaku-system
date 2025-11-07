<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AvailabilitySubmission extends Model
{
    protected $fillable = ['user_id', 'start_date', 'end_date', 'special_requests'];

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
