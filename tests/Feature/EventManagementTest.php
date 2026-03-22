<?php

use App\Models\Event;
use App\Models\User;

test('an authenticated user can create an event', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('events.store'), [
        'title' => 'Investor Mixer',
        'description' => 'A curated networking night for founders and operators.',
        'event_date' => now()->addWeek()->toDateString(),
        'location' => 'London',
    ]);

    $response
        ->assertRedirect(route('events.index'))
        ->assertSessionHas('status', 'Event created successfully.');

    $this->assertDatabaseHas('events', [
        'user_id' => $user->id,
        'title' => 'Investor Mixer',
        'location' => 'London',
    ]);
});

test('users can only see their own events on the index page', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Event::factory()->for($user)->create(['title' => 'My Planning Session']);
    Event::factory()->for($otherUser)->create(['title' => 'Hidden Event']);

    $response = $this->actingAs($user)->get(route('events.index'));

    $response->assertOk();
    $response->assertSee('My Planning Session');
    $response->assertDontSee('Hidden Event');
});

test('users cannot update events they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $event = Event::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)->put(route('events.update', $event), [
        'title' => 'Hijacked Event',
        'description' => 'Changed',
        'event_date' => now()->addWeek()->toDateString(),
        'location' => 'Paris',
    ]);

    $response->assertNotFound();

    expect($event->fresh()->title)->not->toBe('Hijacked Event');
});

test('dashboard counts events scheduled for today as upcoming', function () {
    $user = User::factory()->create();

    Event::factory()->for($user)->create(['event_date' => today()->toDateString()]);
    Event::factory()->for($user)->past()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('1');
});
