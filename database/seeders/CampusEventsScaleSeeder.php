<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusEventsScaleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /*
        |--------------------------------------------------------------------------
        | 1. Create 10 venues
        |--------------------------------------------------------------------------
        */

        $venueIds = [];

        for ($i = 1; $i <= 10; $i++) {
            $venueIds[] = DB::table('venues')->insertGetId([
                'name' => "Campus Venue $i",
                'location' => "Campus Building $i",
                'capacity' => 100 + ($i * 50),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Create 200 participants
        |--------------------------------------------------------------------------
        */

        $participants = [];

        for ($i = 1; $i <= 200; $i++) {
            $participants[] = [
                'name' => "Participant $i",
                'email' => "participant$i@example.com",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($participants, 100) as $chunk) {
            DB::table('participants')->insert($chunk);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Create 20,000 events
        |--------------------------------------------------------------------------
        */

        $eventChunk = [];

        $phrases = [
            'Join us for an exciting campus activity.',
            'Discover new skills through practical learning.',
            'Meet fellow students and share your knowledge.',
            'Participate in an educational student gathering.',
            'Experience an interactive university workshop.',
        ];

        for ($i = 1; $i <= 20000; $i++) {
            $description = $phrases[$i % count($phrases)];

            // Only 50 events contain the word quartz.
            if ($i <= 50) {
                $description .= ' Special quartz technology exhibition.';
            }

            // Only 50 events use ultraviolet.
            $dressCode = $i <= 50
                ? 'ultraviolet'
                : ($i % 2 === 0 ? 'casual' : 'formal');

            $details = [
                'dress_code' => $dressCode,
                'capacity' => 30 + ($i % 171),
                'tags' => [
                    'campus',
                    $i % 2 === 0 ? 'technology' : 'community',
                ],
            ];

            $eventChunk[] = [
                'title' => "Campus Event $i",
                'venue_id' => $venueIds[($i - 1) % 10],
                'description' => $description,
                'details' => json_encode($details),
                'starts_at' => $now->copy()
                    ->addDays($i % 365)
                    ->setTime(9 + ($i % 9), 0)
                    ->toDateTimeString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Insert 500 events per batch.
            if (count($eventChunk) === 500) {
                DB::table('events')->insert($eventChunk);
                $eventChunk = [];
            }
        }

        // Insert remaining events.
        if (!empty($eventChunk)) {
            DB::table('events')->insert($eventChunk);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Register participants for the first 100 events
        |--------------------------------------------------------------------------
        */

        $registrations = [];

        for ($eventId = 1; $eventId <= 100; $eventId++) {
            for ($j = 0; $j < 2; $j++) {
                $participantId = (($eventId + $j - 1) % 200) + 1;

                $registrations[] = [
                    'event_id' => $eventId,
                    'participant_id' => $participantId,
                    'status' => 'registered',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('event_participant')->insert($registrations);

        echo "20,000 events created successfully!";
    }
}
