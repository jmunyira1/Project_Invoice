<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganisationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $org = Organisation::updateOrCreate(
            ['slug' => 'demo-company'],
            [
                'name' => 'Demo Company',
                'slug' => 'demo-company',
                'email' => 'info@demo.com',
                'phone' => '+254 711 318 428',
                'address' => 'Nairobi, Kenya',
                'currency' => 'KES',
                'default_template_id' => Template::where('slug', 'classic')->value('id'),
            ]
        );

        // ── Super admin user ───────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('20252025'),
                'is_super_admin' => true,
                'email_verified_at' => now(),
                'organisation_id' => $org->id,
                'role' => 'owner',
            ]
        );
        $org = Organisation::updateOrCreate(
            ['slug' => 'logia-tech'],
            [
                'name' => 'Logia Tech',
                'slug' => 'logia-tech',
                'email' => 'munyira@munyira.co.ke',
                'phone' => '+254 711 318 428',
                'address' => 'Nairobi, Kenya',
                'currency' => 'KES',
                'default_template_id' => Template::where('slug', 'classic')->value('id'),
            ]
        );

        // ── Super admin user ───────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@munyira.co.ke'],
            [
                'name' => 'Munyira Joseph',
                'email' => 'munyira@munyira.co.ke',
                'password' => Hash::make('P@55w0rd'),
                'is_super_admin' => true,
                'email_verified_at' => now(),
                'organisation_id' => $org->id,
                'role' => 'owner',
            ]
        );

    }
}
