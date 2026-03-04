<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Create Event</h1>

        <form method="POST" action="{{ route('events.store') }}">
            @csrf

            <div class="mb-4">
                <label>Title</label>
                <input type="text" name="title" class="border p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label>Description</label>
                <textarea name="description" class="border p-2 w-full"></textarea>
            </div>

            <div class="mb-4">
                <label>Date</label>
                <input type="date" name="event_date" class="border p-2 w-full" required>
            </div>

            <div class="mb-4">
                <label>Location</label>
                <input type="text" name="location" class="border p-2 w-full" required>
            </div>

            <button class="bg-green-500 text-black px-4 py-2 rounded">
                Save Event
            </button>
        </form>
    </div>
</x-app-layout>