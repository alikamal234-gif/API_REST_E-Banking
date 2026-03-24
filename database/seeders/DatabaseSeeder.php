<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $users = User::factory(10)->create();

    $accounts = Account::factory(10)->create();

    foreach ($accounts as $account) {
        $account->users()->attach(
            $users->random(rand(1, 2))->pluck('id')->toArray()
        );
    }
    }
}
