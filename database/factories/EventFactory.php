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

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_date' => $date->format('Y-m-d'),
            'location' => fake()->city().', '.fake()->country(),
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
