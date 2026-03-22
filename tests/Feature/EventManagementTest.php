<?php

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

test('an authenticated user can create an event', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('events.store'), [
        'title' => 'Investor Mixer',
        'description' => 'A curated networking night for founders and operators.',
        'event_date' => now()->addWeek()->toDateString(),
        'start_time' => '18:00',
        'end_time' => '20:30',
        'status' => Event::STATUS_CONFIRMED,
        'category' => 'Networking',
        'location' => 'London',
        'organizer_name' => 'Aisha Morgan',
        'organizer_email' => 'aisha@example.com',
        'capacity' => 80,
        'visibility' => Event::VISIBILITY_WORKSPACE,
        'reminder_minutes' => 60,
    ]);

    $event = Event::firstWhere('user_id', $user->id);

    $response
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHas('status', 'Event created successfully.');

    $this->assertDatabaseHas('events', [
        'user_id' => $user->id,
        'title' => 'Investor Mixer',
        'category' => 'Networking',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]);
});

test('an authenticated user can create an event with ajax', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson(route('events.store'), [
        'title' => 'AJAX Planning Session',
        'description' => 'Submitted through fetch.',
        'event_date' => now()->addWeek()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '10:00',
        'status' => Event::STATUS_CONFIRMED,
        'category' => 'Meeting',
        'location' => 'Leeds',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Event created successfully.')
        ->assertJsonPath('event.title', 'AJAX Planning Session')
        ->assertJsonPath('event.category', 'Meeting')
        ->assertJsonPath('event.visibility', Event::VISIBILITY_WORKSPACE);

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

test('authenticated users can search visible events created by other users with ajax filters', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create([
        'name' => 'Shared Planner',
        'email' => 'shared-planner@example.com',
    ]);

    Event::factory()->for($owner)->create([
        'title' => 'Cross-Team Summit',
        'description' => 'Shared planning event.',
        'location' => 'Birmingham',
        'category' => 'Conference',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]);

    Event::factory()->for($owner)->create([
        'title' => 'Private Board Review',
        'visibility' => Event::VISIBILITY_PRIVATE,
    ]);

    $response = $this->actingAs($viewer)->getJson(route('events.search', [
        'q' => 'Shared Planner',
        'scope' => 'shared',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]));

    $response
        ->assertOk()
        ->assertJsonPath('meta.count', 1)
        ->assertJsonPath('events.0.title', 'Cross-Team Summit')
        ->assertJsonPath('events.0.owner_name', 'Shared Planner')
        ->assertJsonPath('events.0.is_owner', false);
});

test('guests can view public event pages but not private events', function () {
    $publicEvent = Event::factory()->create([
        'title' => 'Public Showcase',
        'visibility' => Event::VISIBILITY_PUBLIC,
    ]);
    $privateEvent = Event::factory()->create([
        'title' => 'Private Briefing',
        'visibility' => Event::VISIBILITY_PRIVATE,
    ]);

    $this->get(route('events.show', $publicEvent))
        ->assertOk()
        ->assertSee('Public Showcase');

    $this->get(route('events.show', $privateEvent))
        ->assertForbidden();
});

test('users cannot update events they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $event = Event::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)->put(route('events.update', $event), [
        'title' => 'Hijacked Event',
        'description' => 'Changed',
        'event_date' => now()->addWeek()->toDateString(),
        'start_time' => '10:00',
        'end_time' => '11:00',
        'status' => Event::STATUS_CONFIRMED,
        'category' => 'Meeting',
        'location' => 'Paris',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]);

    $response->assertForbidden();

    expect($event->fresh()->title)->not->toBe('Hijacked Event');
});

test('admins can edit any event', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $event = Event::factory()->create();

    $this->actingAs($admin)
        ->get(route('events.edit', $event))
        ->assertOk();
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
        'start_time' => '14:00',
        'end_time' => '15:00',
        'status' => Event::STATUS_CONFIRMED,
        'category' => 'Review',
        'location' => 'Manchester',
        'organizer_name' => 'Morgan Lee',
        'organizer_email' => 'morgan@example.com',
        'capacity' => 24,
        'visibility' => Event::VISIBILITY_PUBLIC,
        'reminder_minutes' => 15,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Event updated successfully.')
        ->assertJsonPath('event.title', 'Updated Brief')
        ->assertJsonPath('event.location', 'Manchester')
        ->assertJsonPath('event.visibility', Event::VISIBILITY_PUBLIC);

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updated Brief',
        'location' => 'Manchester',
        'visibility' => Event::VISIBILITY_PUBLIC,
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

test('an authenticated user can export a single event to ics', function () {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'title' => 'Calendar Ready Event',
        'visibility' => Event::VISIBILITY_WORKSPACE,
    ]);

    $response = $this->actingAs($user)->get(route('events.export', $event));

    $response
        ->assertOk()
        ->assertHeader('content-type', 'text/calendar; charset=UTF-8');

    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
    expect($response->getContent())->toContain('SUMMARY:Calendar Ready Event');
});

test('an authenticated user can export their calendar feed', function () {
    $user = User::factory()->create();
    Event::factory()->for($user)->create(['title' => 'Feed Event One']);
    Event::factory()->for($user)->create(['title' => 'Feed Event Two']);

    $response = $this->actingAs($user)->get(route('events.calendar'));

    $response->assertOk();
    expect($response->getContent())->toContain('Feed Event One');
    expect($response->getContent())->toContain('Feed Event Two');
});

test('an authenticated user can send an event reminder manually', function () {
    Notification::fake();

    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'reminder_minutes' => 60,
        'organizer_email' => $user->email,
    ]);

    $response = $this->actingAs($user)->post(route('events.send-reminder', $event));

    $response
        ->assertRedirect(route('events.show', $event))
        ->assertSessionHas('status', 'Reminder sent successfully.');

    Notification::assertSentTo($user, EventReminderNotification::class);
    expect($event->fresh()->reminder_sent_at)->not->toBeNull();
});

test('the reminder command sends due reminders', function () {
    Notification::fake();
    Carbon::setTestNow('2026-03-22 08:00:00');

    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create([
        'title' => 'Reminder Event',
        'event_date' => '2026-03-22',
        'start_time' => '08:10:00',
        'end_time' => '09:00:00',
        'reminder_minutes' => 15,
        'status' => Event::STATUS_CONFIRMED,
        'organizer_email' => $user->email,
    ]);

    Artisan::call('events:send-reminders');

    Notification::assertSentTo($user, EventReminderNotification::class);
    expect($event->fresh()->reminder_sent_at)->not->toBeNull();

    Carbon::setTestNow();
});

test('dashboard counts events scheduled for today as upcoming', function () {
    $user = User::factory()->create();

    Event::factory()->for($user)->create(['event_date' => today()->toDateString()]);
    Event::factory()->for($user)->past()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertSee('1');
});
