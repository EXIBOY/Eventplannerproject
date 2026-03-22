<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new users receive starter events after registration', function () {
    $this->post('/register', [
        'name' => 'Starter User',
        'email' => 'starter@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'starter@example.com')->firstOrFail();

    expect($user->events)->toHaveCount(count(User::defaultEventTemplates()));
    expect($user->events->pluck('title'))->toContain('Spring Brand Launch');
});

test('default starter events can replace placeholder events with english seed data', function () {
    $user = User::factory()->create();
    $user->events()->create([
        'title' => 'Lorem ipsum',
        'description' => 'Placeholder copy.',
        'event_date' => today()->toDateString(),
        'location' => 'Unknown',
    ]);

    $user->seedDefaultEvents(replaceExisting: true);
    $user->refresh();

    expect($user->events)->toHaveCount(count(User::defaultEventTemplates()));
    expect($user->events->pluck('title'))->toContain('Spring Brand Launch');
    expect($user->events->pluck('title'))->not->toContain('Lorem ipsum');
});
