# Week 11 - Optimized & Advanced Queries (Campus Events at Scale)

**Project:** Campus Events Management System  
**Framework:** Laravel 11 / 12  
**Database:** PostgreSQL 17 / 18  

**Group Members:**
1. AUSTRIA, Norwin L.
2. BATI, Daisuke Reggie C.
3. CARAIG, Kurt John P.

---

## 1. Project Overview

This repository contains the Week 11 Laboratory Assessment for Web Development, extending our Campus Events Management System from Week 10 to demonstrate database query optimization at scale.

Our database is seeded with **20,000 events**, **10 venues**, **200 participants**, and registrations for the first **100 events**. Through this exercise, we identify and fix the N+1 query problem, write reusable and chainable Eloquent local scopes, perform JSONB and full-text searches, and analyze query execution plans with `EXPLAIN (ANALYZE)` in PostgreSQL.

---

## 2. Query Count Comparison (N+1 Investigation)

The `/events` route displays the first 50 events, including each event's title, venue name, and participant count.

| Implementation | Route | Total Queries | Query Log Breakdown |
|---|---|:---:|---|
| **Naive Implementation** | `/events/naive` | **101 queries** | 1 query for 50 events + 50 queries for venues + 50 queries for participants inside the loop. |
| **Optimized Implementation** | `/events` | **2 queries** | 1 query for events with `participants_count` subquery + 1 eager loaded query for venues using `WHERE "id" IN (...)`. |

### Explanation of the Difference:
In the naive implementation, accessing `$event->venue` and `$event->participants` inside the Blade loop triggered lazy loading for every individual record, producing 101 database queries ($1 + 50 + 50 = 101$) and creating a severe N+1 bottleneck. In the optimized implementation, eager loading the relationship via `with('venue')` and aggregating participant totals via `withCount('participants')` consolidated data retrieval into just 2 database queries, achieving a 98% reduction in database round-trips.

To prevent lazy loading in development, we added `Model::preventLazyLoading(! app()->isProduction())` in `AppServiceProvider.php`, which immediately flags any accidental lazy loading with a `LazyLoadingViolationException`.

---

## 3. EXPLAIN (ANALYZE) Benchmark Results

Each query was tested under `EXPLAIN (ANALYZE)` in PostgreSQL using the rare values from Step 2 (`dress_code = 'ultraviolet'` and full-text keyword `'solstice'`), which appear in fewer than 1% of rows ($0.25\%$) to ensure high query selectivity.

### Benchmark Table

| Query Form | Index Status | Index Name | PostgreSQL Plan Type | Execution Time | Planning Time |
|---|---|---|---|:---:|:---:|
| **JSONB Arrow** (`details->>'dress_code' = 'ultraviolet'`) | No index | None | `Seq Scan on events` | **7.252 ms** | 2.118 ms |
| **JSONB Arrow** (`details->>'dress_code' = 'ultraviolet'`) | Expression B-tree | `events_dress_code_idx` | `Index Scan using events_dress_code_idx` | **0.234 ms** | 7.907 ms |
| **JSONB Containment** (`details @> '{"dress_code": "ultraviolet"}'`) | No index | None | `Seq Scan on events` | **8.082 ms** | 0.225 ms |
| **JSONB Containment** (`details @> '{"dress_code": "ultraviolet"}'`) | GIN JSONB | `events_details_gin_idx` | `Bitmap Heap Scan using events_details_gin_idx` | **41.784 ms** | 0.655 ms |
| **Full-Text Search** (`to_tsvector @@ plainto_tsquery('solstice')`) | No index | None | `Seq Scan on events` | **425.331 ms** | 0.274 ms |
| **Full-Text Search** (`to_tsvector @@ plainto_tsquery('solstice')`) | GIN Full-text | `events_description_fts_idx` | `Bitmap Heap Scan using events_description_fts_idx` | **0.157 ms** | 23.045 ms |
| **JSONB Arrow** (`details->>'dress_code' = 'ultraviolet'`) | GIN Containment Only (*Unused Index*) | `events_details_gin_idx` | `Seq Scan on events` (*Index Ignored*) | **11.598 ms** | 2.981 ms |

### Explanation of Indexes:
1. **Expression B-tree Index (`events_dress_code_idx`):** Indexes the scalar string extracted from `((details->>'dress_code'))`. It provides direct, logarithmic lookup (`Index Scan`), accelerating query execution from 7.252 ms down to **0.234 ms** (~31x speedup).
2. **GIN JSONB Containment Index (`events_details_gin_idx`):** Built with `jsonb_path_ops`, this index maps hashed JSON paths and values to support containment checks using the `@>` operator.
3. **GIN Full-Text Index (`events_description_fts_idx`):** Built on `to_tsvector('english', description)`. It indexes lexemes to provide near-instant full-text search, reducing search time from 425.331 ms down to **0.157 ms** (over **2,700x speedup**).
4. **Unused Index Demonstration:** When running the arrow equality query `WHERE details->>'dress_code' = 'ultraviolet'` in the presence of only a containment GIN index, PostgreSQL performed a Sequential Scan (11.598 ms) and ignored the GIN index. This happens because the default GIN operator class (`jsonb_ops` / `jsonb_path_ops`) only supports containment operators (`@>`, `?`), not scalar string equality (`->> =`). Since the query operator does not match the index operator class, the planner must scan the entire table.

---

## 4. Keeping Indexes Across Migrations (Step 8)

All custom database indexes are defined within migration files (`database/migrations/2026_11_09_054614_add_advanced_indexes_to_events_table.php`).

```php
public function up(): void
{
    // B-tree expression index for arrow extraction
    DB::statement("CREATE INDEX events_dress_code_idx ON events ((details->>'dress_code'))");

    // GIN index for JSONB containment
    DB::statement("CREATE INDEX events_details_gin_idx ON events USING GIN (details jsonb_path_ops)");

    // GIN index for full-text search
    DB::statement("CREATE INDEX events_description_fts_idx ON events USING GIN (to_tsvector('english', description))");
}
```

### Why Raw SQL Statements Are Necessary:
Raw SQL statements (`DB::statement()`) are required because Laravel's Schema Blueprint does not natively support PostgreSQL-specific index types—specifically, functional B-tree expressions (indexing `((details->>'key'))`), GIN indexes with specialized operator classes (`jsonb_path_ops`), and functional GIN indexes on `to_tsvector()`. Placing these raw statements inside migration files guarantees that all indexes survive and are automatically recreated whenever running:

```bash
php artisan migrate:fresh --seed
```

---

## 5. Choosing Columns Wisely: JSONB vs Relational Columns (Step 9)

### 1. Information Stored in JSONB (`details`):
- **Stored Attribute:** `dress_code` (along with `capacity` and `tags`).
- **Reason:** Dress codes represent flexible, semi-structured event attributes that differ significantly depending on the event type (e.g., formal gala, casual meetup, sports tournament, laboratory attire). Storing this in a JSONB column avoids schema churn, allows dynamic keys without adding sparsely-populated table columns, and avoids altering the table definition as new event attributes emerge.

### 2. Information Kept in a Normal Column:
- **Stored Attribute:** `starts_at` (along with `title`, `venue_id`, and `description`).
- **Reason:** Event dates and times require strict temporal data integrity, chronological sorting (`ORDER BY starts_at`), timezone-aware comparisons (`WHERE starts_at >= now()`), and high-performance B-tree range indexing. Keeping `starts_at` in a dedicated relational `timestamp` column enables native database-level validation and allows the query planner to optimize chronological event queries efficiently.

---

## 6. Local Query Scopes (Step 3)

We implemented reusable local query scopes in the `Event` model (`app/Models/Event.php`):

| Scope Method | Signature | Purpose & Description |
|---|---|---|
| `scopeUpcoming` | `scopeUpcoming(Builder $query): Builder` | Filters events where `starts_at >= now()`, returning only upcoming scheduled events. |
| `scopeAtVenue` | `scopeAtVenue(Builder $query, int $venueId): Builder` | Parameterized scope that filters events hosted at a specific venue ID (`where('venue_id', $venueId)`). |

### Chaining Scopes in Controller:
As demonstrated in `EventController::scoped()`, these local scopes can be cleanly chained together in a single Eloquent query:

```php
$events = Event::upcoming()
    ->atVenue(1)
    ->with('venue')
    ->withCount('participants')
    ->orderBy('starts_at')
    ->get();
```

---

## 7. What We Learned (Technical Reflection)

Through this assessment, we learned how to systematically diagnose, measure, and fix database performance bottlenecks in a Laravel and PostgreSQL architecture:

1. **Eliminating the N+1 Problem:** We observed firsthand how lazy loading inside loops degrades application throughput by sending 101 separate queries for just 50 records. Applying eager loading with `with('venue')` and relationship counting with `withCount('participants')` slashed query volume down to only 2 queries (a 98% reduction). Enforcing `Model::preventLazyLoading()` in local development provides an essential safety net that catches unoptimized queries before reaching production.
2. **Empirical Verification with EXPLAIN (ANALYZE):** Rather than guessing query efficiency, `EXPLAIN (ANALYZE)` allowed us to observe the PostgreSQL query planner in action. Adding a GIN full-text index reduced description search execution time from **425.331 ms** down to **0.157 ms** (over **2,700x faster**), while an expression B-tree index brought JSONB arrow filtering down to **0.234 ms** (over **30x faster**).
3. **Index Operator Compatibility:** A key technical takeaway was discovering why PostgreSQL ignored an existing GIN index when executing an arrow equality query (`details->>'dress_code' = 'ultraviolet'`), falling back to a sequential scan. We learned that the query planner strictly evaluates operator classes: default GIN indexes only support containment (`@>`), not scalar equality (`=`). Choosing the right index type for the specific query operator is critical to achieving index-driven performance.

---

## 8. Git Repository Information

- **Repository URL:** [WebDev-Week11-Group3](https://github.com/daisukereggiebati-max/WebDev-Week11-Group3)
- **Branch:** `main`
- **Submission Document:** `WebDev-Week11-Assessment-Caraig_Austria_Bati.pdf`
