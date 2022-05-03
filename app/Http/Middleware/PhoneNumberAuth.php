<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhoneNumberAuth
{
    public function handle(Request $request, Closure $next)
    {
        $phoneNumber = $request->header('X-Phone-Number');

        $validated = Validator::make(
            ['phone_number' => $phoneNumber],
            ['phone_number' => ['required', 'digits:11', 'exists:users,phone_number']],
        )->validate();

        $user = User::firstWhere('phone_number', $validated['phone_number']);
        Auth::login($user);

        return $next($request);
    }
}
