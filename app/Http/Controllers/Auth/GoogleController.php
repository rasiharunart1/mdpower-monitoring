<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            Auth::login($user);
            // Cek apakah user benar-benar login
            // dd(Auth::user()); // bisa aktifkan untuk debug
            return redirect()->intended('/dashboard');
        }

        $user = User::where('email', $googleUser->email)->first();
        if ($user) {
            $user->update(['google_id' => $googleUser->id,'avatar' => $googleUser->avatar ?? $user->avatar]);
            Auth::login($user);
            return redirect()->intended('/dashboard');
        }

        // Buat user baru
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'avatar' => $googleUser->avatar, // Simpan avatar jika ada
            'password' => bcrypt('dummy-password'),
            'email_verified_at' => now()
        ]);

        $user->update([
            'google_id' => $googleUser->id,
            'avatar' => $googleUser->avatar
        ]); // Simpan avatar jika ada

        Auth::login($user);
        return redirect()->intended('/dashboard');
    } catch (Exception $e) {
        return redirect('/')->with('error', 'Autentikasi gagal: ' . $e->getMessage());
    }
}

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
