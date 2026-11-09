<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Note;

class CampusEventSeeder extends Seeder
{
    public function run(): void
    {
        // CREATE 3 VENUES

        $venues = collect([
            [
                'name' => 'University Auditorium',
                'location' => 'Main Campus',
                'capacity' => 500
            ],
            [
                'name' => 'Computer Laboratory',
                'location' => 'IT Building',
                'capacity' => 100
            ],
            [
                'name' => 'University Gymnasium',
                'location' => 'Sports Complex',
                'capacity' => 1000
            ],
        ])->map(fn ($data) => Venue::create($data));

        // CREATE 6 EVENTS

        $events = collect([
            ['venue_id' => $venues[0]->id, 'title' => 'IT Seminar', 'description' => 'Technology seminar', 'starts_at' => '2026-11-10 09:00:00'],
            ['venue_id' => $venues[0]->id, 'title' => 'Leadership Forum', 'description' => 'Student leadership event', 'starts_at' => '2026-11-12 10:00:00'],
            ['venue_id' => $venues[1]->id, 'title' => 'Programming Workshop', 'description' => 'Coding workshop', 'starts_at' => '2026-11-15 13:00:00'],
            ['venue_id' => $venues[1]->id, 'title' => 'Database Training', 'description' => 'Database training session', 'starts_at' => '2026-11-17 09:00:00'],
            ['venue_id' => $venues[2]->id, 'title' => 'Sports Festival', 'description' => 'Annual sports event', 'starts_at' => '2026-11-20 08:00:00'],
            ['venue_id' => $venues[2]->id, 'title' => 'Campus Concert', 'description' => 'University music event', 'starts_at' => '2026-11-22 18:00:00'],
        ])->map(fn ($data) => Event::create($data));

        // CREATE 8 PARTICIPANTS

        $participants = collect([
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@example.com'],
            ['name' => 'Maria Santos', 'email' => 'maria@example.com'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro@example.com'],
            ['name' => 'Ana Garcia', 'email' => 'ana@example.com'],
            ['name' => 'Mark Lopez', 'email' => 'mark@example.com'],
            ['name' => 'Sofia Ramos', 'email' => 'sofia@example.com'],
            ['name' => 'Carlo Mendoza', 'email' => 'carlo@example.com'],
            ['name' => 'Ella Torres', 'email' => 'ella@example.com'],
        ])->map(fn ($data) => Participant::create($data));

        // CREATE 12 REGISTRATIONS

        $registrations = [
            [0, 0, 'registered'],
            [0, 1, 'registered'],
            [0, 2, 'waitlisted'],
            [0, 3, 'registered'],

            [1, 0, 'registered'],
            [1, 4, 'waitlisted'],

            [2, 1, 'registered'],
            [2, 5, 'registered'],

            [3, 2, 'waitlisted'],
            [3, 6, 'registered'],

            [4, 3, 'registered'],
            [5, 7, 'waitlisted'],
        ];

        foreach ($registrations as [$eventIndex, $participantIndex, $status]) {
            $events[$eventIndex]->participants()->attach(
                $participants[$participantIndex]->id,
                ['status' => $status]
            );
        }

        // CREATE 6 NOTES
        // 3 NOTES FOR EVENTS

        $events[0]->notes()->create([
            'content' => 'Prepare the seminar materials.'
        ]);

        $events[1]->notes()->create([
            'content' => 'Confirm the guest speakers.'
        ]);

        $events[2]->notes()->create([
            'content' => 'Prepare the computer laboratory.'
        ]);

        // 3 NOTES FOR VENUES

        $venues[0]->notes()->create([
            'content' => 'Check the auditorium sound system.'
        ]);

        $venues[1]->notes()->create([
            'content' => 'Check all laboratory computers.'
        ]);

        $venues[2]->notes()->create([
            'content' => 'Prepare the sports equipment.'
        ]);

        $this->command->info('Campus Events data successfully created!');
    }
}