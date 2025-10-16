<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserImage;
use Faker\Factory as Faker;

class UserImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Получаем все ID пользователей (для случайной привязки)
        $userIds = User::pluck('id')->toArray();

        $batch = [];
        $total = 100000;

        for ($i = 0; $i < $total; $i++) {
            $batch[] = [
                'user_id' => $faker->randomElement($userIds),
                'image' => 'https://picsum.photos/seed/' . $faker->uuid . '/640/480',
                'created_at' => now()
            ];

            // вставляем пакетами для ускорения
            if (count($batch) >= 1000) {
                UserImage::insert($batch);
                $batch = [];
            }
        }

        // вставляем оставшиеся
        if (!empty($batch)) {
            UserImage::insert($batch);
        }
    }
}
