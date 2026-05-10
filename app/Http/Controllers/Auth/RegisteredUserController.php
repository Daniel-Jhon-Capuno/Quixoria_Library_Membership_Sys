<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipTier;
use App\Models\Subscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student', // Default role
        ]);

        // Auto-assign basic tier subscription for students
        if ($user->role === 'student') {
            $basicTier = MembershipTier::where('name', 'Basic')->first();
            if ($basicTier) {
                Subscription::create([
                    'user_id' => $user->id,
                    'membership_tier_id' => $basicTier->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => null,
                    'amount_paid' => 0,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('student.dashboard.index', absolute: false));
    }
}
