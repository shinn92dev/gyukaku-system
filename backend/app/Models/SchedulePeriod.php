<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchedulePeriod extends Model
{
    /** @use HasFactory<\Database\Factories\SchedulePeriodFactory> */
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'is_current',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get schedules for the current period
     */
    public static function current(): ?SchedulePeriod
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Set this period as current and unset others
     */
    public function updateCurrent(): void
    {
        static::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
