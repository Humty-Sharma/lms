<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('app.superadmin_name', env('SUPERADMIN_NAME', 'Super Administrator'));
        $email = config('app.superadmin_email', env('SUPERADMIN_EMAIL', 'superadmin@example.com'));
        $plainPassword = env('SUPERADMIN_PASSWORD', 'Password123!');
        $role = 'superadmin';

        $existing = DB::selectOne('SELECT id FROM users WHERE email = ?', [$email]);
        $now = Carbon::now();

        if ($existing) {
            DB::statement(
                'UPDATE users SET name = ?, updated_at = ? WHERE email = ?',
                [$name, $now, $email]
            );
            return;
        }

        $hashedPassword = Hash::make($plainPassword);

        DB::statement(
            'INSERT INTO users (company_id, name, email, password, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [null, $name, $email, $hashedPassword, $role, $now, $now]
        );
    }
    }
}

