<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TicketResourcesAccessMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $ticket = $request->route('ticket');
        $user = auth()->user();

        $roleType = match ($role) {
            'admin' => 0,
            'customer' => 1,
            'employee' => 2,
            'backend' => 3,
            default => 1,
        };
        //Customer verification
        if ($roleType === 1 && $ticket->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }
        //Employee verification
        if ($roleType === 2 && !$ticket->assigned_agent->contains($user)) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }
        //Admin verification
        if ($roleType === 0 && $user->type !== 0) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }
        //Backend verification
        if ($roleType === 3 && $user->type == 1 ) {
            return response()->json([
                'message' => 'You are not authorized to perform this action.',
            ], 403);
        }


        return $next($request);
    }
}
