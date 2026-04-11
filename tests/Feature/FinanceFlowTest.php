<?php

namespace Tests\Feature;

use App\Models\Members;
use App\Models\Payment;
use App\Models\PaymentConfiguration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FinanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_membership_payment_initiation_creates_pending_payment_and_redirects_to_gateway(): void
    {
        config()->set('services.paystack.secret_key', 'sk_test_123');
        $member = $this->createMember();
        PaymentConfiguration::create([
            'code' => 'MEMBERSHIP',
            'title' => 'Annual Membership Fee',
            'description' => 'Membership payment',
            'amount' => 70,
            'currency' => 'NGN',
            'is_active' => true,
        ]);

        Http::fake([
            'https://api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://paystack.test/checkout',
                ],
            ]),
        ]);

        $response = $this->actingAs($member)->post(route('payments.start', ['code' => 'MEMBERSHIP']));

        $response->assertRedirect('https://paystack.test/checkout');
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'member_id' => $member->id,
            'payment_type' => 'other',
            'status' => 'pending',
            'amount' => 70,
        ]);
    }

    public function test_finance_export_returns_csv_response(): void
    {
        $admin = $this->createMember(Members::ROLE_ADMIN);
        Payment::create([
            'member_id' => $admin->id,
            'reference' => 'ACATA-PAY-CSV001',
            'gateway' => 'paystack',
            'payment_type' => 'membership',
            'status' => 'success',
            'amount' => 70,
            'currency' => 'NGN',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.finance.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('ACATA-PAY-CSV001', $response->streamedContent());
    }

    public function test_paystack_webhook_is_idempotent_for_same_reference(): void
    {
        config()->set('services.paystack.webhook_secret', 'whsec_test');
        $member = $this->createMember();

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ACATA-PAY-IDEMPOTENT',
                'amount' => 7000,
                'currency' => 'NGN',
                'metadata' => [
                    'member_id' => $member->id,
                    'payment_type' => 'membership',
                ],
                'customer' => [
                    'email' => $member->email,
                ],
            ],
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'whsec_test');

        $this->postJson(route('webhooks.paystack'), $payload, ['x-paystack-signature' => $signature])
            ->assertOk();

        $this->postJson(route('webhooks.paystack'), $payload, ['x-paystack-signature' => $signature])
            ->assertOk();

        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'reference' => 'ACATA-PAY-IDEMPOTENT',
            'status' => 'success',
            'member_id' => $member->id,
        ]);
    }

    public function test_paystack_webhook_rejects_invalid_signature(): void
    {
        config()->set('services.paystack.webhook_secret', 'whsec_test');

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ACATA-PAY-BAD-SIGNATURE',
                'amount' => 7000,
                'currency' => 'NGN',
            ],
        ];

        $this->postJson(route('webhooks.paystack'), $payload, ['x-paystack-signature' => 'invalid'])
            ->assertUnauthorized();

        $this->assertDatabaseCount('payments', 0);
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
