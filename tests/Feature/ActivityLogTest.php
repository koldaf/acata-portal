<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Members;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_route_creates_activity_log_record(): void
    {
        $member = $this->createMember();

        $this->actingAs($member)->get(route('member.dashboard'))->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'member_id' => $member->id,
            'route_name' => 'member.dashboard',
            'http_method' => 'GET',
        ]);
    }

    public function test_guest_request_does_not_create_activity_log_record(): void
    {
        $this->get(route('login'))->assertOk();

        $this->assertDatabaseCount('activity_logs', 0);
    }

    private function createMember(string $role = Members::ROLE_MEMBER): Members
    {
        return Members::create([
            'title' => 'Mr.',
            'first_name' => 'Test',
            'middle_name' => null,
            'last_name' => ucfirst($role),
            'email' => strtolower($role) . rand(1000, 9999) . '@example.com',
            'email_verified' => 'yes',
            'password' => 'password123',
            'membership_type' => 'Professional',
            'member_id' => 'ACATA-TEST-' . rand(1000, 9999),
            'status' => 'active',
            'role' => $role,
            'created_on' => now()->toDateString(),
        ]);
    }
}
