<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Events</h2>
        <p class="text-3xl font-bold mt-2">{{ $eventCount }}</p>
    </div>

    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Upcoming Events</h2>
        <p class="text-3xl font-bold mt-2">{{ $upcomingEvents }}</p>
    </div>

</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
