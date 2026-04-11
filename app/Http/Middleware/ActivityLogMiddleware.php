<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $member = $request->user();
        if (!$member) {
            return $response;
        }

        $payload = $request->except(['password', 'password_confirmation', '_token']);

        ActivityLog::create([
            'member_id' => $member->id,
            'action' => ($request->route()?->getName() ?? 'unknown') . ' [' . $request->method() . ']',
            'http_method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'path' => $request->path(),
            'response_status' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => empty($payload) ? null : $payload,
            'performed_at' => now(),
        ]);

        return $response;
    }
}
