<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating; // Đảm bảo đã import model Rating
use Faker\Factory as Faker;

class RatingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 1000) as $index) {
            Rating::create([
                'rating_id' => $faker->randomNumber(9),
                'user_id' => $faker->numberBetween(1, 6),
                'id_view_query' => $faker->numberBetween(60, 65),
                'content' => $faker->text(50),
                'rating' => $faker->numberBetween(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
