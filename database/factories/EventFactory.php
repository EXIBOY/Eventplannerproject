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
            ],
            [
                'title' => 'Product Demo Day',
                'description' => 'A client-facing demo day with product walkthroughs, live questions, and follow-up action notes.',
                'location' => 'MediaCity, Salford',
            ],
            [
                'title' => 'Team Workshop',
                'description' => 'A practical workshop focused on collaboration, process improvements, and the next sprint goals.',
                'location' => 'Temple Meads, Bristol',
            ],
            [
                'title' => 'Networking Breakfast',
                'description' => 'A small morning event for partners, founders, and community guests to exchange updates and ideas.',
                'location' => 'Northern Quarter, Manchester',
            ],
            [
                'title' => 'Client Review Session',
                'description' => 'A review meeting covering campaign performance, feedback, and agreed next steps with the client team.',
                'location' => 'The Mailbox, Birmingham',
            ],
            [
                'title' => 'Community Open Day',
                'description' => 'An open day for attendees, volunteers, and sponsors with check-in coordination and welcome briefings.',
                'location' => 'Royal Albert Dock, Liverpool',
            ],
        ]);

        return [
            'user_id' => User::factory(),
            'title' => $template['title'],
            'description' => $template['description'],
            'event_date' => $date->format('Y-m-d'),
            'location' => $template['location'],
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
