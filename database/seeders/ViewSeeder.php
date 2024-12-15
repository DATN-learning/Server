<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\View; // Đảm bảo đã import model View
use Faker\Factory as Faker;


class ViewSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $lession_id = [
        'lession-231020246-28-167185cda0a9f1',
        'lession-231020246-28-167185da0eca91',
        'lession-231020246-28-267185e2513947',
        'lession-231020246-28-367185f2080c89',
        'lession-231020246-28-167185f93b2e9b',
        'lession-231020246-28-267185ff3e1ee8',
        'lession-231020246-28-36718603fc2f11',
        'lession-231020246-29-26718a45b1fcfd',
        'lession-231020246-28-36718a5036e3ec',
        'lession-231020246-29-46718a5b9ae603',
        'lession-231020246-28-56718a64bd8b2a',
        'lession-231020246-28-16718a78e5ef9b',
        'lession-231020246-29-26718a81828aea',
        'lession-231020246-28-36718a8a8a2790',
        'lession-301020246-28-167218fc36e91f',
        'lession-301020246-30-26721900c4a11d',
        'lession-301020246-28-1672191871f1ff',
        'lession-301020246-30-2672191c4c5d6d',
        'lession-31120246-28-1672716e0edf0e',
        'lession-31120246-31-2672717907bd5b',
        'lession-31120246-31-26727190e3d8b0',
        'lession-31120246-31-267271b0386c03',
        'lession-31120246-28-1672720048b26d',
        'lession-31120246-33-267272070062b3',
        'lession-31120246-33-16727212a6eebb',
        ];
        foreach (range(1, 1000) as $index) {
            View::create([
                'view_id' => $faker->randomNumber(9),
                'user_id' => $faker->numberBetween(1, 9),
                'id_view_query' => $lession_id[array_rand($lession_id)],
                'time_view' => $faker->numberBetween(60, 900),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
