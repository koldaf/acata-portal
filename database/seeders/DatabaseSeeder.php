<?php

namespace Database\Seeders;

use App\Models\Members;
use App\Models\PaymentConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Members::count() === 0) {
            Members::create([
                'title' => 'Mr.',
                'first_name' => 'System',
                'middle_name' => null,
                'last_name' => 'Administrator',
                'email' => env('SUPER_ADMIN_EMAIL', 'admin@acata.org'),
                'password' => env('SUPER_ADMIN_PASSWORD', 'ChangeMe@123'),
                'membership_type' => 'Professional',
                'phone' => null,
                'affiliation' => 'ACATA',
                'job_title' => 'Platform Administrator',
                'country' => 'Nigeria',
                'status' => 'active',
                'email_verified' => 'yes',
                'role' => Members::ROLE_SUPER_ADMIN,
                'role_assigned_at' => now(),
                'created_on' => now()->toDateString(),
            ]);
        }

        PaymentConfiguration::updateOrCreate(
            ['code' => 'MEMBERSHIP'],
            [
                'title' => 'Annual Membership Fee',
                'description' => 'Standard annual membership payment.',
                'amount' => 70,
                'currency' => 'NGN',
                'is_active' => true,
            ]
        );

        PaymentConfiguration::updateOrCreate(
            ['code' => 'EVENT_REGISTRATION'],
            [
                'title' => 'Event Registration Fee',
                'description' => 'General event registration payment item.',
                'amount' => 25,
                'currency' => 'NGN',
                'is_active' => true,
            ]
        );

        PaymentConfiguration::updateOrCreate(
            ['code' => 'RESOURCE_ACCESS'],
            [
                'title' => 'Premium Resource Access',
                'description' => 'Access fee for premium ACATA resources.',
                'amount' => 15,
                'currency' => 'NGN',
                'is_active' => true,
            ]
        );
    }
}
