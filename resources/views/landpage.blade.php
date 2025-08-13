@extends('layout.template')

@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-lg p-4" style="max-width:400px;width:100%;">
            <div class="text-center mb-4">
                <img src="{{ asset('img/plts-logo.png') }}" alt="PLTS Logo" style="width:80px;">
                <h2 class="mt-3">Welcome to PLTS Monitoring</h2>
                <p class="text-muted">Pantau sistem PLTS Anda dengan mudah & real-time.</p>
            </div>
            <a href="{{ url('auth/google') }}" class="btn btn-danger btn-block mb-3">
                <i class="fab fa-google mr-2"></i> Login dengan Google
            </a>
            <div class="text-center">
                <small>Belum punya akun? Login saja dengan Google!</small>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Font Awesome untuk logo Google jika belum ada -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
