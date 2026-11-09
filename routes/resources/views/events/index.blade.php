<!DOCTYPE html>
<html>
<head>
    <title>Campus Events</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        h1 {
            color: #222;
        }

        .count {
            background: #dff0d8;
            padding: 15px;
            margin-bottom: 20px;
        }

        .event {
            background: white;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <h1>Campus Events</h1>

    <div class="count">
        <strong>Database Queries:</strong>
        {{ $queryCount }}
    </div>

    @foreach ($events as $event)

        <div class="event">

            <h3>{{ $event->title }}</h3>

            <p>
                Venue:
                {{ $event->venue?->name ?? 'No venue' }}
            </p>

            <p>
                Participants:

                @if ($fixed)
                    {{ $event->participants_count }}
                @else
                    {{ $event->participants->count() }}
                @endif
            </p>

        </div>

    @endforeach

</body>
</html>