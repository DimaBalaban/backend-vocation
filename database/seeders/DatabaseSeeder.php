<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()->create([
            'name' => 'Dmitry',
            'email' => 'dmi@examp.com',
            'password' => Hash::make('d16091609'),


        ]);
    }
}
