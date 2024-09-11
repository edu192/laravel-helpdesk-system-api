<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    : array
    {
        $request->authenticate();

        $request->session()->regenerate();
        $token = \auth()->user()->createToken('auth_token');

        return ['token' => $token->plainTextToken];
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    : Response
    {
        if ($token = PersonalAccessToken::where('tokenable_id', $request->user()->id)->first()) {
            $token->delete();
        }
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
