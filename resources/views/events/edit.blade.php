<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Event</h1>

        <form method="POST" action="{{ route('events.update', $event) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Title</label>
                <input type="text" name="title" 
                       value="{{ $event->title }}" 
                       class="border p-2 w-full">
            </div>

            <div class="mb-4">
                <label>Description</label>
                <textarea name="description" 
                          class="border p-2 w-full">{{ $event->description }}</textarea>
            </div>

            <div class="mb-4">
                <label>Date</label>
                <input type="date" name="event_date" 
                       value="{{ $event->event_date }}" 
                       class="border p-2 w-full">
            </div>

            <div class="mb-4">
                <label>Location</label>
                <input type="text" name="location" 
                       value="{{ $event->location }}" 
                       class="border p-2 w-full">
            </div>

            <button class="bg-blue-500 text-white px-4 py-2 rounded">
                Update Event
            </button>
        </form>
    </div>
</x-app-layout>