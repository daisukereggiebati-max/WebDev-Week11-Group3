# Week 11 – Optimized & Advanced Queries

## Campus Events at Scale

Project: Campus Events Management System  
Framework: Laravel  
Database: PostgreSQL 17

Group 3 – Members:
1. Austria, Norwin L.
2. Bati, Daisuke Reggie C.
3. Caraig, Kurt John P.

---

## 1. Project Overview

This project improves the Campus Events Management System by implementing optimized database queries, JSONB filtering, full-text search, reusable query scopes, and database indexing.

Our system contains at least 20,000 events, 200 participants, and registrations for the first 100 events. Through this project, we demonstrate how database optimization techniques can improve query efficiency and performance when handling large amounts of data.

## 2. Query Count Comparison

Our naive implementation uses lazy loading to retrieve the venue and participants of every event.

Our optimized implementation uses eager loading and `withCount()` to retrieve the required information more efficiently.

| Implementation | Query Count |
|---|---|
| Naive implementation | [Insert actual count] |
| Optimized implementation | [Insert actual count] |

**Explanation:**

The naive implementation generates additional database queries for every event because its relationships are loaded individually. The optimized implementation uses eager loading and relationship counting, reducing the number of queries and improving database efficiency.

Based on our comparison, we can understand how eager loading helps reduce unnecessary database queries when retrieving related records.

## 3. EXPLAIN ANALYZE Results

### Query Plan Comparison

| Query | Index | Plan Type | Execution Time |
|---|---|---|---|
| JSONB arrow | No index | [Actual plan] | [Actual time] |
| JSONB arrow | Expression B-tree | [Actual plan] | [Actual time] |
| JSONB containment | No index | [Actual plan] | [Actual time] |
| JSONB containment | GIN JSONB | [Actual plan] | [Actual time] |
| Full-text search | No index | [Actual plan] | [Actual time] |
| Full-text search | GIN Full-text | [Actual plan] | [Actual time] |
| JSONB arrow | GIN containment only | [Actual plan] | [Actual time] |

### Explanation of Indexes

The expression B-tree index is used for JSONB arrow-form filtering because it indexes the extracted `dress_code` value.

The GIN JSONB index supports containment searches using the PostgreSQL `@>` operator.

The GIN full-text index supports searches using PostgreSQL's full-text search functions.

We also learned that an existing index may remain unused if its indexed expression or operator class does not match the query being executed.

## 4. Indexes That Survive Migrations

All our custom indexes are created inside Laravel migration files.

This ensures that the indexes are recreated when running:

```bash
php artisan migrate:fresh --seed
```

Our PostgreSQL-specific indexes use raw SQL statements inside the migration because Laravel's standard schema builder does not directly provide all the required index definitions.

## 5. JSONB vs Normal Columns

**JSONB:**

We stored dress code, capacity, and tags in the JSONB `details` column because these are additional properties that may vary between events. JSONB provides flexibility when storing structured event information.

**Normal Columns:**

We kept title, `venue_id`, description, and `starts_at` as normal columns because they are important fields used frequently for searching, filtering, sorting, and establishing relationships.

Through this implementation, we learned how to choose between flexible JSONB storage and regular database columns based on the purpose of the data.

## 6. Local Query Scopes

| Scope | Purpose |
|---|---|
| `upcoming()` | Retrieves events scheduled for the future. |
| `atVenue($venueId)` | Retrieves events belonging to a particular venue. |

We implemented these scopes in our Event model to make queries reusable and organized.

These scopes can also be chained together to create queries with multiple conditions.

## 7. What We Learned

Through this activity, we learned how to identify and solve the N+1 query problem using eager loading and relationship counting. We also learned how PostgreSQL JSONB queries, full-text search, and database indexes can improve database operations.

We gained a better understanding of how `EXPLAIN ANALYZE` helps us examine query execution plans and compare database performance before and after indexing.

One thing we found challenging was understanding why PostgreSQL sometimes chooses a sequential scan even when an index exists. We learned that the query planner considers the query conditions, available indexes, and estimated execution costs before choosing an execution plan. We would like to explore more about how PostgreSQL determines the most efficient way to execute different queries.

