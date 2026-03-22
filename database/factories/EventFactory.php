<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-2 months', '+4 months');
        $template = fake()->randomElement([
            [
                'title' => 'Quarterly Planning Meeting',
                'description' => 'A planning session to review priorities, assign owners, and confirm the next delivery milestones.',
                'location' => 'Canary Wharf, London',
                'category' => 'Meeting',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'organizer_name' => 'Olivia Carter',
                'organizer_email' => 'olivia@example.com',
                'capacity' => 20,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => 60,
            ],
            [
                'title' => 'Product Demo Day',
                'description' => 'A client-facing demo day with product walkthroughs, live questions, and follow-up action notes.',
                'location' => 'MediaCity, Salford',
                'category' => 'Launch',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '11:00:00',
                'end_time' => '13:00:00',
                'organizer_name' => 'Harper Lewis',
                'organizer_email' => 'harper@example.com',
                'capacity' => 75,
                'visibility' => Event::VISIBILITY_PUBLIC,
                'reminder_minutes' => 1440,
            ],
            [
                'title' => 'Team Workshop',
                'description' => 'A practical workshop focused on collaboration, process improvements, and the next sprint goals.',
                'location' => 'Temple Meads, Bristol',
                'category' => 'Workshop',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '13:30:00',
                'end_time' => '16:00:00',
                'organizer_name' => 'Daniel Brooks',
                'organizer_email' => 'daniel@example.com',
                'capacity' => 18,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => 60,
            ],
            [
                'title' => 'Networking Breakfast',
                'description' => 'A small morning event for partners, founders, and community guests to exchange updates and ideas.',
                'location' => 'Northern Quarter, Manchester',
                'category' => 'Networking',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '08:30:00',
                'end_time' => '10:00:00',
                'organizer_name' => 'Sophia Green',
                'organizer_email' => 'sophia@example.com',
                'capacity' => 45,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => 60,
            ],
            [
                'title' => 'Client Review Session',
                'description' => 'A review meeting covering campaign performance, feedback, and agreed next steps with the client team.',
                'location' => 'The Mailbox, Birmingham',
                'category' => 'Review',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'organizer_name' => 'Mason Bell',
                'organizer_email' => 'mason@example.com',
                'capacity' => 12,
                'visibility' => Event::VISIBILITY_PRIVATE,
                'reminder_minutes' => 15,
            ],
            [
                'title' => 'Community Open Day',
                'description' => 'An open day for attendees, volunteers, and sponsors with check-in coordination and welcome briefings.',
                'location' => 'Royal Albert Dock, Liverpool',
                'category' => 'Volunteer',
                'status' => Event::STATUS_CONFIRMED,
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
                'organizer_name' => 'Grace Murphy',
                'organizer_email' => 'grace@example.com',
                'capacity' => 120,
                'visibility' => Event::VISIBILITY_PUBLIC,
                'reminder_minutes' => 1440,
            ],
        ]);

        return [
            'user_id' => User::factory(),
            'title' => $template['title'],
            'description' => $template['description'],
            'event_date' => $date->format('Y-m-d'),
            'start_time' => $template['start_time'],
            'end_time' => $template['end_time'],
            'status' => $template['status'],
            'category' => $template['category'],
            'location' => $template['location'],
            'organizer_name' => $template['organizer_name'],
            'organizer_email' => $template['organizer_email'],
            'capacity' => $template['capacity'],
            'visibility' => $template['visibility'],
            'reminder_minutes' => $template['reminder_minutes'],
        ];
    }

    public function upcoming(): static
    {
        return $this->state(fn () => [
            'event_date' => fake()->dateTimeBetween('tomorrow', '+4 months')->format('Y-m-d'),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'event_date' => fake()->dateTimeBetween('-4 months', 'yesterday')->format('Y-m-d'),
        ]);
    }
}
