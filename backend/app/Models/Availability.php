<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'user_id', 'work_date', 'lunch', 'dinner', 'special_requests',
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
