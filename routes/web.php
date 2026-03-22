<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $events = Auth::user()
        ->events()
        ->orderBy('event_date')
        ->orderBy('title')
        ->get();

    $today = today();
    $upcomingSchedule = $events
        ->filter(fn (Event $event) => $event->event_date->greaterThanOrEqualTo($today))
        ->values();

    $pastEvents = $events
        ->filter(fn (Event $event) => $event->event_date->lt($today))
        ->sortByDesc('event_date')
        ->values();

    $eventCount = $events->count();
    $upcomingEvents = $upcomingSchedule->count();
    $nextEvent = $upcomingSchedule->first();
    $recentActivity = $pastEvents->take(3);

    return view('dashboard', compact(
        'eventCount',
        'upcomingEvents',
        'nextEvent',
        'upcomingSchedule',
        'recentActivity'
    ));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('events', EventController::class)->except('show');
});

require __DIR__.'/auth.php';
