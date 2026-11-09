<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = [
        'venue_id',
        'title',
        'description',
        'details',
        'starts_at'
    ];

    // Automatically convert database values into PHP types.
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'details' => 'array',
        ];
    }

    // Many events belong to one venue.
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    // One event has many participants.
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(
            Participant::class,
            'event_participant'
        )
        ->withPivot('status')
        ->withTimestamps();
    }

    // One event has many notes.
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    // WEEK 11 LOCAL SCOPE 1:
    // Get events that are scheduled in the future.
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now());
    }

    // WEEK 11 LOCAL SCOPE 2:
    // Get events belonging to a specific venue.
    public function scopeAtVenue(
        Builder $query,
        int $venueId
    ): Builder {
        return $query->where('venue_id', $venueId);
    }
}
