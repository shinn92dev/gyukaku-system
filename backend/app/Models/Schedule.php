<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    protected $fillable = [
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
            'work_date' => 'date',
            'is_understaffed' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Shift, $this>
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }
}
