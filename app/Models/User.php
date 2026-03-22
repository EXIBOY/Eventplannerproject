<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return list<array{title: string, description: string, location: string, day_offset: int, start_time: string, end_time: string, status: string, category: string, organizer_name: string, organizer_email: string, capacity: int|null, visibility: string, reminder_minutes: int|null}>
     */
    public static function defaultEventTemplates(): array
    {
        return [
            [
                'title' => 'Spring Brand Launch',
                'description' => 'An evening launch with media previews, a keynote presentation, and a rooftop networking session for partners.',
                'location' => 'Sea Containers, London',
                'day_offset' => 5,
                'start_time' => '18:30:00',
                'end_time' => '21:30:00',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Launch',
                'organizer_name' => 'Aisha Morgan',
                'organizer_email' => 'events@eventplanner.test',
                'capacity' => 160,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => Event::DEFAULT_REMINDER_MINUTES,
            ],
            [
                'title' => 'Founder Breakfast',
                'description' => 'A curated breakfast for startup founders, angel investors, and creative operators to exchange referrals.',
                'location' => 'Soho Works, Manchester',
                'day_offset' => 12,
                'start_time' => '08:00:00',
                'end_time' => '10:00:00',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Networking',
                'organizer_name' => 'Caleb Hughes',
                'organizer_email' => 'founders@eventplanner.test',
                'capacity' => 40,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => 60,
            ],
            [
                'title' => 'Design Sprint Workshop',
                'description' => 'A half-day workshop covering rapid ideation, prototype reviews, and action planning for product teams.',
                'location' => 'Platform, Birmingham',
                'day_offset' => 21,
                'start_time' => '09:30:00',
                'end_time' => '13:30:00',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Workshop',
                'organizer_name' => 'Nina Patel',
                'organizer_email' => 'workshops@eventplanner.test',
                'capacity' => 24,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => Event::DEFAULT_REMINDER_MINUTES,
            ],
            [
                'title' => 'Summer Client Dinner',
                'description' => 'A private dining experience for top clients with a short awards presentation and entertainment set.',
                'location' => 'The Ivy Asia, Leeds',
                'day_offset' => 40,
                'start_time' => '19:00:00',
                'end_time' => '22:00:00',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Social',
                'organizer_name' => 'Maya Reed',
                'organizer_email' => 'clients@eventplanner.test',
                'capacity' => 60,
                'visibility' => Event::VISIBILITY_PRIVATE,
                'reminder_minutes' => Event::DEFAULT_REMINDER_MINUTES,
            ],
            [
                'title' => 'Community Volunteer Day',
                'description' => 'An all-hands volunteer event with check-in support, branded kits, and transport coordination.',
                'location' => 'Victoria Park, Bristol',
                'day_offset' => -14,
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'status' => Event::STATUS_COMPLETED,
                'category' => 'Volunteer',
                'organizer_name' => 'Imani Cole',
                'organizer_email' => 'community@eventplanner.test',
                'capacity' => 120,
                'visibility' => Event::VISIBILITY_PUBLIC,
                'reminder_minutes' => null,
            ],
            [
                'title' => 'Winter Gala Planning Session',
                'description' => 'Internal pre-production meeting to align vendors, budgets, staging notes, and the guest communications timeline.',
                'location' => 'Studio Three, Liverpool',
                'day_offset' => -35,
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'status' => Event::STATUS_COMPLETED,
                'category' => 'Meeting',
                'organizer_name' => 'Marcus Ellison',
                'organizer_email' => 'production@eventplanner.test',
                'capacity' => 16,
                'visibility' => Event::VISIBILITY_WORKSPACE,
                'reminder_minutes' => null,
            ],
        ];
    }

    public function seedDefaultEvents(bool $replaceExisting = false): void
    {
        if (!$replaceExisting && $this->events()->exists()) {
            return;
        }

        if ($replaceExisting) {
            $this->events()->delete();
        }

        $referenceDate = today();

        $starterEvents = array_map(function (array $template) use ($referenceDate): array {
            return [
                'title' => $template['title'],
                'description' => $template['description'],
                'location' => $template['location'],
                'event_date' => $referenceDate->copy()->addDays($template['day_offset'])->toDateString(),
                'start_time' => $template['start_time'],
                'end_time' => $template['end_time'],
                'status' => $template['status'],
                'category' => $template['category'],
                'organizer_name' => $template['organizer_name'],
                'organizer_email' => $template['organizer_email'],
                'capacity' => $template['capacity'],
                'visibility' => $template['visibility'],
                'reminder_minutes' => $template['reminder_minutes'],
            ];
        }, self::defaultEventTemplates());

        $this->events()->createMany($starterEvents);
    }
}
