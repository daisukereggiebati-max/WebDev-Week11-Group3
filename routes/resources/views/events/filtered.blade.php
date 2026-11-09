<!DOCTYPE html>
<html>
<head>
    <title>JSONB Filter</title>
</head>

<body>

    <h1>JSONB Event Filter</h1>

    <p>
        Filter mode: {{ $mode }}
    </p>

    <p>
        Dress code: {{ $value }}
    </p>

    <h3>Generated SQL</h3>

    <pre>{{ $sql }}</pre>

    <h3>Bindings</h3>

    <pre>{{ json_encode($bindings) }}</pre>

    <h3>Results</h3>

    @forelse ($events as $event)

        <p>
            {{ $event->title }}
            -
            {{ $event->details['dress_code'] ?? '' }}
        </p>

    @empty

        <p>No events found.</p>

    @endforelse

</body>
</html>
