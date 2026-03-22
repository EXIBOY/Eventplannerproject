<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_WORKSPACE = 'workspace';
    public const VISIBILITY_PUBLIC = 'public';

    public const DEFAULT_REMINDER_MINUTES = 1440;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'status',
        'category',
        'location',
        'organizer_name',
        'organizer_email',
        'capacity',
        'visibility',
        'reminder_minutes',
        'reminder_sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'capacity' => 'integer',
            'reminder_minutes' => 'integer',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categoryOptions(): array
    {
        return [
            'Launch' => 'Launch',
            'Meeting' => 'Meeting',
            'Workshop' => 'Workshop',
            'Networking' => 'Networking',
            'Conference' => 'Conference',
            'Social' => 'Social',
            'Volunteer' => 'Volunteer',
            'Review' => 'Review',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_PRIVATE => 'Private',
            self::VISIBILITY_WORKSPACE => 'Workspace',
            self::VISIBILITY_PUBLIC => 'Public',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function reminderOptions(): array
    {
        return [
            15 => '15 minutes before',
            60 => '1 hour before',
            1440 => '1 day before',
            10080 => '1 week before',
        ];
    }

    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $userId);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('event_date', '>=', today()->toDateString());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('event_date', '<', today()->toDateString());
    }

    public function scopeOrderedChronologically(Builder $query): Builder
    {
        return $query
            ->orderBy('event_date')
            ->orderByRaw('CASE WHEN start_time IS NULL THEN 1 ELSE 0 END')
            ->orderBy('start_time')
            ->orderBy('title');
    }

    public function scopeOrderedReverseChronologically(Builder $query): Builder
    {
        return $query
            ->orderByDesc('event_date')
            ->orderByRaw('CASE WHEN start_time IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('start_time')
            ->orderBy('title');
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if ($user?->is_admin) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user): void {
            $builder->where('visibility', self::VISIBILITY_PUBLIC);

            if ($user !== null) {
                $builder
                    ->orWhere('visibility', self::VISIBILITY_WORKSPACE)
                    ->orWhere('user_id', $user->id);
            }
        });
    }

    public function scopeSearchTerm(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $builder) use ($like): void {
            $builder
                ->where('title', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('location', 'like', $like)
                ->orWhere('category', 'like', $like)
                ->orWhere('organizer_name', 'like', $like)
                ->orWhere('organizer_email', 'like', $like)
                ->orWhereHas('user', function (Builder $userQuery) use ($like): void {
                    $userQuery
                        ->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like);
                });
        });
    }

    public function startsAt(): ?CarbonImmutable
    {
        if ($this->event_date === null) {
            return null;
        }

        $time = $this->start_time ?: '09:00:00';

        return CarbonImmutable::parse(
            sprintf('%s %s', $this->event_date->toDateString(), $time),
            config('app.timezone'),
        );
    }

    public function endsAt(): ?CarbonImmutable
    {
        if ($this->event_date === null) {
            return null;
        }

        if ($this->end_time) {
            return CarbonImmutable::parse(
                sprintf('%s %s', $this->event_date->toDateString(), $this->end_time),
                config('app.timezone'),
            );
        }

        if ($this->start_time) {
            return $this->startsAt()?->addHour();
        }

        return null;
    }

    public function reminderAt(): ?CarbonImmutable
    {
        if ($this->reminder_minutes === null) {
            return null;
        }

        return $this->startsAt()?->subMinutes($this->reminder_minutes);
    }

    public function timeLabel(): string
    {
        if ($this->start_time && $this->end_time) {
            return sprintf(
                '%s - %s',
                $this->parseTimeValue($this->start_time)->format('g:i A'),
                $this->parseTimeValue($this->end_time)->format('g:i A'),
            );
        }

        if ($this->start_time) {
            return 'Starts '.$this->parseTimeValue($this->start_time)->format('g:i A');
        }

        return 'All day';
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? Str::headline((string) $this->status);
    }

    public function categoryLabel(): string
    {
        return $this->category ?: 'General';
    }

    public function visibilityLabel(): string
    {
        return self::visibilityOptions()[$this->visibility] ?? Str::headline((string) $this->visibility);
    }

    public function visibilityDescription(): string
    {
        return match ($this->visibility) {
            self::VISIBILITY_PRIVATE => 'Only you can see this event.',
            self::VISIBILITY_PUBLIC => 'Anyone with the link can view this event.',
            default => 'Signed-in workspace users can view this event.',
        };
    }

    private function parseTimeValue(string $time): CarbonImmutable
    {
        $format = str($time)->length() === 5 ? 'H:i' : 'H:i:s';

        return CarbonImmutable::createFromFormat($format, $time);
    }
}
