<?php

namespace Tests\Feature;

use App\Models\Members;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_finance_dashboard(): void
    {
        $response = $this->get(route('admin.finance.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_member_cannot_access_admin_finance_dashboard(): void
    {
        $member = $this->createMember();

        $response = $this->actingAs($member)->get(route('admin.finance.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_finance_dashboard(): void
    {
        $admin = $this->createMember(Members::ROLE_ADMIN);

        $response = $this->actingAs($admin)->get(route('admin.finance.index'));

        $response->assertOk();
        $response->assertSee('Finance Dashboard');
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
