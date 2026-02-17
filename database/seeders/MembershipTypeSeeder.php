<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $now = Carbon::now();
        
        $membershipTypes = [
            [
                'id' => 1,
                'name' => 'Full Membership',
                'payment_link' => 'https://paystack.shop/pay/fm-70',
                'price' => '$70',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 2,
                'name' => 'Student Membership',
                'payment_link' => 'https://paystack.shop/pay/sm-40',
                'price' => '$40',
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'id' => 3,
                'name' => 'Institutional Membership',
                'payment_link' => 'https://paystack.shop/pay/im-150',
                'price' => '$150',
                'created_at' => $now,
                'updated_at' => $now
            ]
        ];

        // Insert data
        DB::table('membership_types')->insert($membershipTypes);
        
        $this->command->info('Membership types seeded successfully!');
    }
}
