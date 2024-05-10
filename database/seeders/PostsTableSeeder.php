<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; //DB::tableの使用許可
use Illuminate\Support\Facades\Hash; //DB::Hashの使用許可

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            [
                'user_id' => '1',
                'post' => 'おはようございます',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '2',
                'post' => 'こんにちは',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '3',
                'post' => 'こんばんは',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '4',
                'post' => '初めまして',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => '5',
                'post' => 'さようなら',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
