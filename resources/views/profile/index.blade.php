@extends('layouts.template')

@section('content')
    <div class="container mt-4">
        <div class="card" style="max-width: 400px; margin: auto;">
            <div class="card-body text-center">
                {{-- Jika kamu ingin menampilkan avatar Google, tambahkan field avatar di DB dan logic saat login --}}
                <img src="{{ $user->avatar }}" alt="Avatar" class="rounded-circle mb-3" width="120" height="120">
                {{-- <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="Avatar"
                    class="rounded-circle mb-3" width="120" height="120"> --}}
                <h4>{{ $user->name }}</h4>
                <p>{{ $user->email }}</p>
                <p class="text-muted">Device Code: {{ $device->device_code }}</p>
                <p class="text-muted">Google ID: {{ $user->google_id }}</p>
            </div>
        </div>
    </div>
@endsection
