<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $planner = User::factory()->create([
            'name' => 'Aisha Morgan',
            'email' => 'planner@example.com',
            'password' => Hash::make('password'),
        ]);

        $planner->events()->createMany([
            [
                'title' => 'Spring Brand Launch',
                'description' => 'An evening launch with media previews, a keynote presentation, and a rooftop networking session for partners.',
                'event_date' => now()->addDays(5)->toDateString(),
                'location' => 'Sea Containers, London',
            ],
            [
                'title' => 'Founder Breakfast',
                'description' => 'A curated breakfast for startup founders, angel investors, and creative operators to exchange referrals.',
                'event_date' => now()->addDays(12)->toDateString(),
                'location' => 'Soho Works, Manchester',
            ],
            [
                'title' => 'Design Sprint Workshop',
                'description' => 'A half-day workshop covering rapid ideation, prototype reviews, and action planning for product teams.',
                'event_date' => now()->addDays(21)->toDateString(),
                'location' => 'Platform, Birmingham',
            ],
            [
                'title' => 'Summer Client Dinner',
                'description' => 'A private dining experience for top clients with a short awards presentation and entertainment set.',
                'event_date' => now()->addDays(40)->toDateString(),
                'location' => 'The Ivy Asia, Leeds',
            ],
            [
                'title' => 'Community Volunteer Day',
                'description' => 'An all-hands volunteer event with check-in support, branded kits, and transport coordination.',
                'event_date' => now()->subDays(14)->toDateString(),
                'location' => 'Victoria Park, Bristol',
            ],
            [
                'title' => 'Winter Gala Planning Session',
                'description' => 'Internal pre-production meeting to align vendors, budgets, staging notes, and the guest communications timeline.',
                'event_date' => now()->subDays(35)->toDateString(),
                'location' => 'Studio Three, Liverpool',
            ],
        ]);

        User::factory(2)
            ->create()
            ->each(fn (User $user) => Event::factory()->count(3)->for($user)->create());
    }
}
