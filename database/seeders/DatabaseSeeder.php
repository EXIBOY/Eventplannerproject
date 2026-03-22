<?php

namespace Database\Seeders;

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
        User::query()->updateOrCreate([
            'email' => 'planner@example.com',
        ], [
            'name' => 'Aisha Morgan',
            'is_admin' => true,
            'password' => Hash::make('password'),
        ]);

        $missingUsers = max(0, 3 - User::query()->count());

        if ($missingUsers > 0) {
            User::factory($missingUsers)->create();
        }

        User::query()
            ->get()
            ->each(fn (User $user) => $user->seedDefaultEvents(replaceExisting: true));
    }
}
