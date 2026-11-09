<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Browse campus events and compare event query performance.">
    <title>{{ config('app.name', 'Campus Events') }}</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Arial, sans-serif;
            color: #172033;
            background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem clamp(1.25rem, 6vw, 5rem);
            background: #fff;
            border-bottom: 1px solid #e3e8f0;
        }

        .brand {
            color: #173a72;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        nav a {
            color: #34445e;
            font-weight: 600;
            text-decoration: none;
        }

        nav a:hover,
        nav a:focus-visible {
            color: #1d5fc1;
            text-decoration: underline;
        }

        main {
            width: min(100% - 2.5rem, 68rem);
            margin: 0 auto;
            padding: clamp(3rem, 9vw, 7rem) 0;
        }

        .hero {
            padding: clamp(2rem, 6vw, 4.5rem);
            border-radius: 1.25rem;
            background: #173a72;
            color: #fff;
        }

        .eyebrow {
            margin: 0 0 0.75rem;
            color: #c7dcff;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 42rem;
            margin: 0;
            font-size: clamp(2.25rem, 6vw, 4rem);
            line-height: 1.08;
        }

        .intro {
            max-width: 38rem;
            margin: 1.25rem 0 2rem;
            color: #e2ebfb;
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .button {
            display: inline-block;
            padding: 0.85rem 1.2rem;
            border-radius: 0.5rem;
            background: #fff;
            color: #173a72;
            font-weight: 700;
            text-decoration: none;
        }

        .button:hover,
        .button:focus-visible {
            background: #e8f0ff;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 17rem), 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .card {
            padding: 1.5rem;
            border: 1px solid #e3e8f0;
            border-radius: 0.9rem;
            background: #fff;
        }

        .card h2 {
            margin: 0 0 0.5rem;
            font-size: 1.15rem;
        }

        .card p {
            margin: 0 0 1rem;
            color: #53627a;
            line-height: 1.6;
        }

        .card a {
            color: #1d5fc1;
            font-weight: 700;
            text-decoration: none;
        }

        .card a:hover,
        .card a:focus-visible {
            text-decoration: underline;
        }

        @media (max-width: 32rem) {
            header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <a class="brand" href="{{ url('/') }}">Campus Events</a>
        <nav aria-label="Main navigation">
            <a href="{{ url('/events') }}">Events</a>
            <a href="{{ url('/events/naive') }}">Query comparison</a>
        </nav>
    </header>

    <main>
        <section class="hero" aria-labelledby="welcome-heading">
            <p class="eyebrow">Campus Events Management</p>
            <h1 id="welcome-heading">Find your next campus event.</h1>
            <p class="intro">
                Browse upcoming events, see their venues and participant counts,
                and explore how eager loading improves query performance.
            </p>
            <a class="button" href="{{ url('/events') }}">Browse events</a>
        </section>

        <section class="cards" aria-label="Explore the site">
            <article class="card">
                <h2>Campus events</h2>
                <p>View events alongside their venues and participant totals.</p>
                <a href="{{ url('/events') }}">View events</a>
            </article>
            <article class="card">
                <h2>Query comparison</h2>
                <p>Compare the naive event query with the eager-loaded version.</p>
                <a href="{{ url('/events/naive') }}">View query comparison</a>
            </article>
        </section>
    </main>
</body>
</html>
