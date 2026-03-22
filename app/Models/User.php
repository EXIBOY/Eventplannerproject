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
            'password' => 'hashed',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return list<array{title: string, description: string, location: string, day_offset: int}>
     */
    public static function defaultEventTemplates(): array
    {
        return [
            [
                'title' => 'Spring Brand Launch',
                'description' => 'An evening launch with media previews, a keynote presentation, and a rooftop networking session for partners.',
                'location' => 'Sea Containers, London',
                'day_offset' => 5,
            ],
            [
                'title' => 'Founder Breakfast',
                'description' => 'A curated breakfast for startup founders, angel investors, and creative operators to exchange referrals.',
                'location' => 'Soho Works, Manchester',
                'day_offset' => 12,
            ],
            [
                'title' => 'Design Sprint Workshop',
                'description' => 'A half-day workshop covering rapid ideation, prototype reviews, and action planning for product teams.',
                'location' => 'Platform, Birmingham',
                'day_offset' => 21,
            ],
            [
                'title' => 'Summer Client Dinner',
                'description' => 'A private dining experience for top clients with a short awards presentation and entertainment set.',
                'location' => 'The Ivy Asia, Leeds',
                'day_offset' => 40,
            ],
            [
                'title' => 'Community Volunteer Day',
                'description' => 'An all-hands volunteer event with check-in support, branded kits, and transport coordination.',
                'location' => 'Victoria Park, Bristol',
                'day_offset' => -14,
            ],
            [
                'title' => 'Winter Gala Planning Session',
                'description' => 'Internal pre-production meeting to align vendors, budgets, staging notes, and the guest communications timeline.',
                'location' => 'Studio Three, Liverpool',
                'day_offset' => -35,
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
            ];
        }, self::defaultEventTemplates());

        $this->events()->createMany($starterEvents);
    }
}
