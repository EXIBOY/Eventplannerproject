<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
    Route::get('/events/calendar.ics', [EventController::class, 'calendar'])->name('events.calendar');
    Route::post('/events/{event}/send-reminder', [EventController::class, 'sendReminder'])->name('events.send-reminder');
    Route::resource('events', EventController::class)->except('show');
});

Route::get('/events/{event}/calendar.ics', [EventController::class, 'export'])->name('events.export');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

require __DIR__.'/auth.php';
