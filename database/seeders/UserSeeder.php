<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = [];

        for ($i = 0; $i < 10000; $i++) {
            $users[] = [
                'name' => $faker->name(),
                'city' => $faker->city(),
                'created_at' => now(),
            ];

            // вставляем пакетами, чтобы не тормозило
            if (count($users) >= 1000) {
                User::insert($users);
                $users = [];
            }
        }

        // оставшиеся записи
        if (!empty($users)) {
            User::insert($users);
        }
    }
}
