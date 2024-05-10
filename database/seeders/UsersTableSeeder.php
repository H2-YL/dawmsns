<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; //DB::tableの使用許可
use Illuminate\Support\Facades\Hash; //DB::Hashの使用許可
use Illuminate\Support\Str; //DB::Strの使用許可

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => '田中太郎',
                'email' => Str::random(5).'@mail.com',
                'password' => Hash::make('tarou11'),
                'bio' => '宜しくお願いします。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '鈴木史郎',
                'email' => Str::random(5).'@mail.com',
                'password' => Hash::make('suzuki22'),
                'bio' => '宜しくお願いしますん。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '吉田絵里',
                'email' => Str::random(5).'@mail.com',
                'password' => Hash::make('yosida33'),
                'bio' => '宜しくお願いしました。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '早川達也',
                'email' => Str::random(5).'@mail.com',
                'password' => Hash::make('hayakawa44'),
                'bio' => '宜しくお願いしましょう。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '吉本卓也',
                'email' => Str::random(5).'@mail.com',
                'password' => Hash::make('yosimoto55'),
                'bio' => '宜しくお願いしません。',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
