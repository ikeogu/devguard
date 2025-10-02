<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use ZojaTech\DevGuard\Models\DevUser;

class DevUserSeeder extends Seeder
{
    public function run()
    {
        if (! class_exists(DevUser::class)) {
            $this->command->warn("DevUser model not found. Skipping seeder.");
            return;
        }

        if (DevUser::count() === 0) {
            DevUser::create([
                'name' => 'Dev Admin',
                'email' => 'dev@local.test',
                'password' => Hash::make('password'), // default password
                'remember_token' => Str::random(10),
                'role' => 'dev_guard',
                'email_verified_at' => now(),
            ]);

            $this->command->info("Default DevUser created: dev@local.test / password");
        } else {
            $this->command->warn("DevUser already exists, skipping.");
        }
    }
}
