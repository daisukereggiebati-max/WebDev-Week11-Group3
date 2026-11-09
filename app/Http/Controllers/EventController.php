<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    // 1. NAIVE VERSION
    // Demonstrates N+1 query problems and lazy loading.
    public function naive()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $events = Event::orderBy('id')
            ->limit(50)
            ->get();

        // Deliberately access relationships without eager loading.
        foreach ($events as $event) {
            $event->venue;
            $event->participants;
        }

        $queryCount = count(DB::getQueryLog());

        DB::disableQueryLog();

        return view('events.index', [
            'events' => $events,
            'queryCount' => $queryCount,
            'fixed' => false,
        ]);
    }

    // 2. OPTIMIZED VERSION
    // Uses eager loading to reduce database queries.
    public function index()
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $events = Event::with('venue')
            ->withCount('participants')
            ->orderBy('id')
            ->limit(50)
            ->get();

        $queryCount = count(DB::getQueryLog());

        DB::disableQueryLog();

        return view('events.index', [
            'events' => $events,
            'queryCount' => $queryCount,
            'fixed' => true,
        ]);
    }

    // 3. LOCAL SCOPES DEMONSTRATION
    // Uses the upcoming() and atVenue() local scopes.
    public function scoped()
    {
        $events = Event::upcoming()
            ->atVenue(1)
            ->with('venue')
            ->withCount('participants')
            ->orderBy('starts_at')
            ->get();

        return view('events.index', [
            'events' => $events,
            'queryCount' => null,
            'fixed' => true,
        ]);
    }

    public function filter(Request $request)
    {
        $validated = $request->validate([
            'dress_code' => ['sometimes', 'string', 'max:255'],
            'mode' => ['sometimes', Rule::in(['arrow', 'contains'])],
        ]);

        $value = $validated['dress_code'] ?? 'formal';
        $mode = $validated['mode'] ?? 'arrow';

        $query = Event::query();

        if ($mode === 'arrow') {
            $query->where('details->dress_code', $value);
        } else {
            $query->whereJsonContains(
                'details',
                ['dress_code' => $value]
            );
        }

        $query->limit(50);

        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $events = $query->get();

        return view('events.filtered', compact(
            'events',
            'sql',
            'bindings',
            'value',
            'mode'
        ));
    }

    public function search(Request $request)
    {
        $search = $request->query('q', '');

        $query = Event::query();

        if ($search !== '') {
            $query->whereFullText('description', $search);
        }

        $query->limit(50);

        $sql = $query->toSql();
        $bindings = $query->getBindings();
        $events = $query->get();

        return view('events.search', compact(
            'events',
            'sql',
            'bindings',
            'search'
        ));
    }
}
