<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者ユーザーを作成
        User::create([
            'email' => 'admin@coachtech.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $user1 = User::create([
            'name' => '西 怜奈',
            'email' => 'reina.n@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);

        $user2 = User::create([
            'name' => '山田 太郎',
            'email' => 'taro.y@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);

        $user3 = User::create([
            'name' => '増田 一世',
            'email' => 'issei.m@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);

        $user4 = User::create([
            'name' => '山本 敬吉',
            'email' => 'keikichi.y@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);

        $user5 = User::create([
            'name' => '秋田 朋美',
            'email' => 'tomomi.a@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);

        $user6 = User::create([
            'name' => '中西 教夫',
            'email' => 'norio.n@coachtech.com',
            'password' => Hash::make('12345678'),
            'role' => User::ROLE_USER,
        ]);
    }
}
