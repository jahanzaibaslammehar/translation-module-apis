<?php

namespace Database\Seeders;

use App\Models\Translation;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Insert 100k translations in chunks of 1000
        $batchSize = 1000;
        $total = 100000;

        for ($i = 0; $i < $total / $batchSize; $i++) {
            \App\Models\Translation::factory($batchSize)->create();
        }

        $this->call([
            UserSeeder::class,
        ]);
    }
}
