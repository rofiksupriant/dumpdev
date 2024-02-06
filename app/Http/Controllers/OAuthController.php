<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {
                Auth::login($finduser);
            } else {
                $newUser = User::create(['name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'password' => 'default123'
                    ]
                );

                Auth::login($newUser);
            }
            return redirect()->route("dashboard");
        } catch
        (Exception $e) {
            dd($e);
        }
    }
}
