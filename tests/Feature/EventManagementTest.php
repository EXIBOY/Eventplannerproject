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

test('an authenticated user can create an event with ajax', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('events.store'), [
        'title' => 'AJAX Planning Session',
        'description' => 'Submitted through fetch.',
        'event_date' => now()->addWeek()->toDateString(),
        'location' => 'Leeds',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Event created successfully.')
        ->assertJsonPath('event.title', 'AJAX Planning Session');

    $this->assertDatabaseHas('events', [
        'user_id' => $user->id,
        'title' => 'AJAX Planning Session',
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

test('authenticated users can search events created by any user with ajax', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create([
        'name' => 'Shared Planner',
        'email' => 'shared-planner@example.com',
    ]);

    Event::factory()->for($owner)->create([
        'title' => 'Cross-Team Summit',
        'description' => 'Shared planning event.',
        'location' => 'Birmingham',
    ]);

    $response = $this->actingAs($viewer)->getJson(route('events.search', [
        'q' => 'Shared Planner',
    ]));

    $response
        ->assertOk()
        ->assertJsonPath('meta.count', 1)
        ->assertJsonPath('events.0.title', 'Cross-Team Summit')
        ->assertJsonPath('events.0.owner_name', 'Shared Planner')
        ->assertJsonPath('events.0.is_owner', false);
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

test('an authenticated user can update an event with ajax', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'title' => 'Initial Brief',
        'description' => 'Original copy.',
        'location' => 'Bristol',
    ]);

    $response = $this->actingAs($user)->putJson(route('events.update', $event), [
        'title' => 'Updated Brief',
        'description' => 'Refined through AJAX.',
        'event_date' => now()->addDays(10)->toDateString(),
        'location' => 'Manchester',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Event updated successfully.')
        ->assertJsonPath('event.title', 'Updated Brief')
        ->assertJsonPath('event.location', 'Manchester');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updated Brief',
        'location' => 'Manchester',
    ]);
});

test('an authenticated user can delete an event with ajax', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create();

    $response = $this->actingAs($user)->postJson(route('events.destroy', $event), [
        '_method' => 'DELETE',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Event deleted successfully.')
        ->assertJsonPath('event_id', $event->id);

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
    ]);
});

test('dashboard counts events scheduled for today as upcoming', function () {
    $user = User::factory()->create();

    Event::factory()->for($user)->create(['event_date' => today()->toDateString()]);
    Event::factory()->for($user)->past()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('1');
});
