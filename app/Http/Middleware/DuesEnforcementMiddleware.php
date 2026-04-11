<?php

namespace App\Http\Middleware;

use App\Models\Members;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DuesEnforcementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $member = $request->user();

        if (!$member instanceof Members) {
            return $next($request);
        }

        if (!$member->shouldBeBlockedForDues()) {
            return $next($request);
        }

        $window = Members::financialYearWindow();
        $message = 'Membership dues are required to access this feature. '
            . 'Please pay your annual dues for the ' . $window['label']
            . ' financial year (Aug-Jul). Grace period ended on '
            . $window['grace_ends_at']->format('M j, Y') . '.';

        return redirect()->route('dashboard.payments')->with('error', $message);
    }
}
