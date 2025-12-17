<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \Illuminate\Support\Carbon $work_date
 */
class Schedule extends Model
{
    /** @use HasFactory<\Database\Factories\ScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'schedule_period_id',
        'work_date',
        'type',
        'is_understaffed',
    ];

    protected function setTypeAttribute(string $value): void
    {
        $this->attributes['type'] = strtolower($value);
    }

    protected function casts(): array
    {
        return [
            'work_date' => 'date:Y-m-d',
            'is_understaffed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<SchedulePeriod, $this>
     */
    public function schedulePeriod(): BelongsTo
    {
        return $this->belongsTo(SchedulePeriod::class);
    }

    /**
     * @return HasMany<Shift, $this>
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}
