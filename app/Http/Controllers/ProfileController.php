<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $device = $user->device;
        if (!$user) {
            return redirect('/login')->with('error', 'Anda harus login terlebih dahulu');
        }

        return view('profile.index', compact('user', 'device'));
    }
}
