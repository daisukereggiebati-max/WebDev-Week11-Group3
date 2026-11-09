<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Note;

class CampusEventReportController extends Controller
{
    public function index()
    {
        // Select sample records for the reports

        $chosenVenue = Venue::firstOrFail();
        $chosenEvent = Event::firstOrFail();
        $chosenParticipant = Participant::firstOrFail();
        $chosenNote = Note::firstOrFail();

        // QUERY 1:
        // Every venue with the number of events it hosts

        $venues = Venue::withCount('events')->get();

        // QUERY 2:
        // All events at one chosen venue

        $eventsAtVenue = $chosenVenue->events;

        // QUERY 3:
        // All participants of one chosen event
        // Include each person's registration status

        $participantsAtEvent = $chosenEvent
            ->participants()
            ->get();

        // QUERY 4:
        // All events one chosen participant has joined

        $eventsJoined = $chosenParticipant->events;

        // QUERY 5:
        // Events with at least one waitlisted participant

        $waitlistedEvents = Event::whereHas(
            'participants',
            function ($query) {
                $query->where(
                    'event_participant.status',
                    'waitlisted'
                );
            }
        )->get();

        // QUERY 6:
        // All notes of one chosen event and one chosen venue

        $eventNotes = $chosenEvent->notes;
        $venueNotes = $chosenVenue->notes;

        // QUERY 7:
        // One note's owner and owner type

        $selectedNote = Note::with('noteable')
            ->findOrFail($chosenNote->id);

        return view('reports.index', compact(
            'chosenVenue',
            'chosenEvent',
            'chosenParticipant',
            'venues',
            'eventsAtVenue',
            'participantsAtEvent',
            'eventsJoined',
            'waitlistedEvents',
            'eventNotes',
            'venueNotes',
            'selectedNote'
        ));
    }
}