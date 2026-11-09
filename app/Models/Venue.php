<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'location',
        'capacity'
    ];

    // One venue has many events
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // One venue has many notes
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}