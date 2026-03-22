<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'capacity' => $this->filled('capacity') ? (int) $this->input('capacity') : null,
            'reminder_minutes' => $this->filled('reminder_minutes') ? (int) $this->input('reminder_minutes') : null,
            'organizer_email' => $this->filled('organizer_email')
                ? strtolower((string) $this->input('organizer_email'))
                : null,
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'event_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'required_with:start_time', 'after:start_time'],
            'status' => ['required', 'string', Rule::in(array_keys(Event::statusOptions()))],
            'category' => ['required', 'string', Rule::in(array_keys(Event::categoryOptions()))],
            'location' => ['required', 'string', 'max:255'],
            'organizer_name' => ['nullable', 'string', 'max:255'],
            'organizer_email' => ['nullable', 'email:rfc', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'visibility' => ['required', 'string', Rule::in(array_keys(Event::visibilityOptions()))],
            'reminder_minutes' => ['nullable', 'integer', Rule::in(array_keys(Event::reminderOptions()))],
        ];
    }
}
