<!DOCTYPE html>
<html>
<head>
    <title>Event Search</title>
</head>

<body>

    <h1>Search Campus Events</h1>

    <form action="{{ url('/events/search') }}" method="GET">

        <input
            type="text"
            name="q"
            placeholder="Search events..."
            value="{{ $search }}"
        >

        <button type="submit">
            Search
        </button>

    </form>

    <h3>Generated SQL</h3>

    <pre>{{ $sql }}</pre>

    <h3>Bindings</h3>

    <pre>{{ json_encode($bindings) }}</pre>

    <h3>Search Results</h3>

    @forelse ($events as $event)

        <div>
            <h4>{{ $event->title }}</h4>

            <p>{{ $event->description }}</p>
        </div>

    @empty

        <p>No results found.</p>

    @endforelse

</body>
</html>
