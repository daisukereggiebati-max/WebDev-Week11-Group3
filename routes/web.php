<?php

use App\Http\Controllers\CampusEventReportController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reports', [
    CampusEventReportController::class,
    'index',
])->name('reports.index');

Route::get('/events/naive', [
    EventController::class,
    'naive',
]);

Route::get('/events/filter', [
    EventController::class,
    'filter',
])->name('events.filter');

Route::get('/events/search', [
    EventController::class,
    'search',
]);

Route::get('/events', [
    EventController::class,
    'index',
]);
