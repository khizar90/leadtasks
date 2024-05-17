<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new Admin();
        $user->name = 'Kevin Anderson';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('qweqwe');
        $user->save();
    }
}
