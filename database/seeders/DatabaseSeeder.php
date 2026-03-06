<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $images = [
            'https://picsum.photos/id/1011/200/200',
            'https://picsum.photos/id/1012/200/200',
            'https://picsum.photos/id/1015/200/200',
            'https://picsum.photos/id/1020/200/200',
            // add more
        ];
        User::factory()->count(210)->create([
            'image' => function () use ($images) {
                return $images[array_rand($images)];
            },
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
