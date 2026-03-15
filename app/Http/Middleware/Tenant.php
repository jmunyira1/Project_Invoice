<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Tenant
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user->organisation_id) {
            abort(403, 'Your account is not linked to any organisation. Please contact your administrator.');
        }

        // Load the organisation once and share it everywhere:
        // - auth()->user()->organisation  (via the relationship)
        // - view()->shared('currentOrg')  (available in all Blade views)
        $org = $user->organisation;
        view()->share('currentOrg', $org);

        return $next($request);
    }
}
