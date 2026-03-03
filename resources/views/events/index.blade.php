<x-app-layout>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">My Events</h1>

        <a href="{{ route('events.create') }}" 
           class="bg-blue-500 text-white px-4 py-2 rounded">
            + Create Event
        </a>

        <div class="mt-6">
            @forelse ($events as $event)
                <div class="bg-white p-4 mb-4 shadow rounded">
                    <h2 class="text-xl font-semibold">{{ $event->title }}</h2>
                    <p>{{ $event->description }}</p>
                    <p class="text-sm text-gray-500">
                        {{ $event->event_date }} | {{ $event->location }}
                    </p>

                    <div class="mt-2">
                        <a href="{{ route('events.edit', $event) }}" 
                           class="text-blue-600">Edit</a>

                        <form action="{{ route('events.destroy', $event) }}" 
                              method="POST" 
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 ml-4">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p>No events yet.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>