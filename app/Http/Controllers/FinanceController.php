<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Members;
use App\Models\PaymentConfiguration;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        $paymentsQuery = $this->applyFinanceFilters(Payment::query()->with('member'), $request)
            ->latest('paid_at')
            ->latest();

        $payments = $paymentsQuery->paginate(20)->withQueryString();

        $filteredPayments = $this->applyFinanceFilters(Payment::query(), $request);
        $successfulPayments = (clone $filteredPayments)->where('status', 'success');
        $summary = [
            'total_revenue' => (clone $successfulPayments)->sum('amount'),
            'total_successful' => (clone $successfulPayments)->count(),
            'pending_payments' => (clone $filteredPayments)->where('status', 'pending')->count(),
            'failed_payments' => (clone $filteredPayments)->whereIn('status', ['failed', 'abandoned'])->count(),
            'membership_revenue' => (clone $filteredPayments)->where('status', 'success')->where('payment_type', 'membership')->sum('amount'),
            'event_revenue' => (clone $filteredPayments)->where('status', 'success')->where('payment_type', 'event')->sum('amount'),
        ];

        return view('admin.finance.index', [
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $payments = $this->applyFinanceFilters(Payment::query()->with('member'), $request)
            ->latest('paid_at')
            ->latest()
            ->get();

        return response()->streamDownload(function () use ($payments): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference', 'Member', 'Email', 'Type', 'Status', 'Amount', 'Currency', 'Gateway', 'Paid At']);

            foreach ($payments as $payment) {
                fputcsv($handle, [
                    $payment->reference,
                    $payment->member?->display_name,
                    $payment->member?->email,
                    $payment->payment_type,
                    $payment->status,
                    $payment->amount,
                    $payment->currency,
                    $payment->gateway,
                    $payment->paid_at?->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, 'finance-report.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function paymentConfigurations(): View
    {
        return view('admin.finance.payment-configurations', [
            'paymentConfigurations' => PaymentConfiguration::query()->latest()->paginate(20),
        ]);
    }

    public function editPaymentConfiguration(PaymentConfiguration $paymentConfiguration): View
    {
        return view('admin.finance.payment-configuration-edit', [
            'paymentConfiguration' => $paymentConfiguration,
        ]);
    }

    public function storePaymentConfiguration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:payment_configurations,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        PaymentConfiguration::create([
            'code' => strtoupper(trim($validated['code'])),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment configuration created successfully.');
    }

    public function updatePaymentConfiguration(Request $request, PaymentConfiguration $paymentConfiguration): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        $paymentConfiguration->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'currency' => strtoupper($validated['currency']),
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Payment configuration updated successfully.');
    }

    public function destroyPaymentConfiguration(PaymentConfiguration $paymentConfiguration): RedirectResponse
    {
        $paymentConfiguration->delete();

        return back()->with('success', 'Payment configuration deleted successfully.');
    }

    public function memberPaymentCatalog(): View
    {
        return view('dashboard.payments', [
            'paymentConfigurations' => PaymentConfiguration::query()
                ->where('is_active', true)
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function startPaymentByCode(Request $request, string $code): RedirectResponse
    {
        $member = Auth::user();
        abort_unless($member instanceof Members, 403);

        $paymentConfiguration = PaymentConfiguration::query()
            ->where('code', strtoupper($code))
            ->where('is_active', true)
            ->first();

        if (!$paymentConfiguration) {
            return back()->with('error', 'This payment option is unavailable.');
        }

        $amount = (float) $paymentConfiguration->amount;
        if ($amount <= 0) {
            return back()->with('error', 'This payment option has an invalid amount.');
        }

        $paymentCode = strtoupper((string) $paymentConfiguration->code);
        $paymentType = $this->mapPaymentTypeFromCode($paymentCode);
        $duesWindow = $paymentType === 'membership' ? Members::financialYearWindow() : null;

        $reference = 'ACATA-PAY-' . Str::upper(Str::random(12));

        $payment = Payment::create([
            'member_id' => $member->id,
            'reference' => $reference,
            'gateway' => 'paystack',
            'payment_type' => $paymentType,
            'status' => 'pending',
            'amount' => $amount,
            'currency' => strtoupper($paymentConfiguration->currency),
            'metadata' => [
                'payment_code' => $paymentCode,
                'payment_title' => $paymentConfiguration->title,
                'dues_financial_year_start' => $duesWindow ? $duesWindow['start']->toDateString() : null,
                'dues_financial_year_end' => $duesWindow ? $duesWindow['end']->toDateString() : null,
            ],
        ]);

        $secretKey = (string) config('services.paystack.secret_key');
        if ($secretKey === '') {
            return back()->with('error', 'Payment gateway is not configured.');
        }

        $response = Http::withToken($secretKey)
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $member->email,
                'amount' => (int) round($amount * 100),
                'reference' => $reference,
                'callback_url' => route('payments.callback'),
                'metadata' => [
                    'member_id' => $member->id,
                    'payment_type' => $paymentType,
                    'payment_code' => $paymentCode,
                    'payment_title' => $paymentConfiguration->title,
                    'dues_financial_year_start' => $duesWindow ? $duesWindow['start']->toDateString() : null,
                    'dues_financial_year_end' => $duesWindow ? $duesWindow['end']->toDateString() : null,
                ],
            ]);

        if (!$response->successful() || !$response->json('status')) {
            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'initialize_response' => $response->json(),
                ]),
            ]);

            return back()->with('error', 'Unable to initialize payment at the moment.');
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'initialize_response' => $response->json('data'),
            ]),
        ]);

        return redirect()->away((string) $response->json('data.authorization_url'));
    }

    public function activityLogs(Request $request): View
    {
        $logsQuery = $this->applyActivityLogFilters(ActivityLog::query()->with('member'), $request)
            ->latest('performed_at');

        return view('admin.finance.activity-logs', [
            'logs' => $logsQuery->paginate(30)->withQueryString(),
        ]);
    }

    public function showActivityLog(ActivityLog $activityLog): View
    {
        $activityLog->load('member');

        $redactedPayload = $this->redactPayload($activityLog->payload);

        return view('admin.finance.activity-log-show', [
            'activityLog' => $activityLog,
            'formattedPayload' => $redactedPayload === null
                ? null
                : json_encode($redactedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function exportActivityLogs(Request $request): StreamedResponse
    {
        $logs = $this->applyActivityLogFilters(ActivityLog::query()->with('member'), $request)
            ->latest('performed_at')
            ->get();

        return response()->streamDownload(function () use ($logs): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Performed At',
                'User',
                'Email',
                'Action',
                'Route',
                'Method',
                'Status',
                'Path',
                'IP Address',
                'User Agent',
                'Payload',
            ]);

            foreach ($logs as $log) {
                $payload = $this->redactPayload($log->payload);

                fputcsv($handle, [
                    $log->performed_at?->toDateTimeString(),
                    $log->member?->display_name ?? 'Unknown',
                    $log->member?->email ?? '',
                    $log->action,
                    $log->route_name,
                    $log->http_method,
                    $log->response_status,
                    $log->path,
                    $log->ip_address,
                    $log->user_agent,
                    $payload === null ? '' : json_encode($payload, JSON_UNESCAPED_SLASHES),
                ]);
            }

            fclose($handle);
        }, 'activity-logs.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function handlePaystackWebhook(Request $request)
    {
        $secret = (string) config('services.paystack.webhook_secret');
        $signature = (string) $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if ($secret === '' || $signature === '') {
            abort(400, 'Webhook configuration is incomplete.');
        }

        $computedSignature = hash_hmac('sha512', $payload, $secret);
        if (!hash_equals($computedSignature, $signature)) {
            abort(401, 'Invalid webhook signature.');
        }

        $event = $request->input('event');
        $data = $request->input('data', []);

        if ($event !== 'charge.success' || !is_array($data)) {
            return response()->json(['message' => 'Webhook acknowledged.']);
        }

        $reference = (string) ($data['reference'] ?? '');
        if ($reference === '') {
            return response()->json(['message' => 'Missing payment reference.'], 422);
        }

        $metadata = is_array($data['metadata'] ?? null) ? $data['metadata'] : [];
        $memberId = $metadata['member_id'] ?? null;
        $member = null;

        if ($memberId) {
            $member = Members::find($memberId);
        }

        if (!$member && !empty($data['customer']['email'])) {
            $member = Members::where('email', $data['customer']['email'])->first();
        }

        $paymentCode = strtoupper((string) ($metadata['payment_code'] ?? ''));
        $paymentType = in_array(($metadata['payment_type'] ?? null), ['membership', 'event', 'resource', 'other'], true)
            ? $metadata['payment_type']
            : $this->mapPaymentTypeFromCode($paymentCode);

        $existingPayment = Payment::where('reference', $reference)->first();
        $effectiveMetadata = array_merge($existingPayment?->metadata ?? [], $metadata, $data);

        if ($paymentType === 'membership' && empty($effectiveMetadata['dues_financial_year_start'])) {
            $duesWindow = Members::financialYearWindow();
            $effectiveMetadata['dues_financial_year_start'] = $duesWindow['start']->toDateString();
            $effectiveMetadata['dues_financial_year_end'] = $duesWindow['end']->toDateString();
        }

        Payment::updateOrCreate(
            ['reference' => $reference],
            [
                'member_id' => $member?->id,
                'gateway' => 'paystack',
                'payment_type' => $paymentType,
                'status' => 'success',
                'amount' => ((int) ($data['amount'] ?? 0)) / 100,
                'currency' => (string) ($data['currency'] ?? 'NGN'),
                'paid_at' => Payment::where('reference', $reference)->value('paid_at') ?: now(),
                'metadata' => $effectiveMetadata,
            ]
        );

        Log::info('Paystack payment recorded', ['reference' => $reference]);

        return response()->json(['message' => 'Payment recorded successfully.']);
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->string('reference')->toString();
        $status = $request->string('status')->toString();

        if ($reference !== '') {
            $payment = Payment::firstOrCreate(
                ['reference' => $reference],
                [
                    'gateway' => 'paystack',
                    'payment_type' => 'membership',
                    'status' => 'pending',
                    'currency' => 'NGN',
                    'amount' => 0,
                    'metadata' => [],
                ]
            );

            $payment->update([
                'status' => in_array($status, ['success', 'failed', 'abandoned'], true) ? $status : $payment->status,
                'metadata' => array_merge($payment->metadata ?? [], [
                    'callback_query' => $request->query(),
                ]),
            ]);
        }

        return redirect()->route('member.dashboard')->with('success', 'Payment callback received. Your transaction status will update after gateway confirmation.');
    }

    private function applyFinanceFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->string('payment_type')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from')->toDateString());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to')->toDateString());
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('member', function ($memberQuery) use ($search) {
                        $memberQuery->search($search);
                    });
            });
        }

        return $query;
    }

    private function applyActivityLogFilters($query, Request $request)
    {
        if ($request->filled('member')) {
            $memberSearch = $request->string('member')->toString();
            $query->whereHas('member', function ($memberQuery) use ($memberSearch) {
                $memberQuery->search($memberSearch);
            });
        }

        if ($request->filled('method')) {
            $query->where('http_method', strtoupper($request->string('method')->toString()));
        }

        if ($request->filled('route_name')) {
            $query->where('route_name', 'like', '%' . $request->string('route_name')->toString() . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('performed_at', '>=', $request->date('date_from')->toDateString());
        }

        if ($request->filled('date_to')) {
            $query->whereDate('performed_at', '<=', $request->date('date_to')->toDateString());
        }

        return $query;
    }

    private function redactPayload(mixed $payload): mixed
    {
        if ($payload === null) {
            return null;
        }

        if (!is_array($payload)) {
            return $payload;
        }

        $redacted = [];
        foreach ($payload as $key => $value) {
            if (is_string($key) && preg_match('/password|token|secret|authorization/i', $key)) {
                $redacted[$key] = '[REDACTED]';
                continue;
            }

            $redacted[$key] = $this->redactPayload($value);
        }

        return $redacted;
    }

    private function mapPaymentTypeFromCode(string $code): string
    {
        return match (strtoupper($code)) {
            'MEMBERSHIP' => 'membership',
            'EVENT_REGISTRATION' => 'event',
            'RESOURCE_ACCESS' => 'resource',
            default => 'other',
        };
    }

}
