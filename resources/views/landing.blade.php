@extends('layouts.auth')

@section('content')
    {{-- <div> --}}
    {{-- <div class="soft-hero position-relative overflow-hidden"> --}}
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="card card-soft border-0" style="max-width: 420px; width: 100%;">
            <div class="card-body p-4 p-md-5 text-center">
                <div class="logo-wrap mb-3">
                    <img src="{{ asset('assets/img/l.png') }}" alt="PLTS Logo" class="img-fluid" style="max-width:56px;">
                </div>

                <h2 class="title mb-2">MEGADATA POWERPLANT</h2>
                <p class="subtitle text-muted mb-4">Realtime Solar Panel Monitoring.</p>

                <a href="{{ url('auth/google') }}" class="btn btn-google-soft mb-3">
                    <i class="fab fa-google"></i>
                    <span>Login dengan Google</span>
                </a>

                {{-- <div class="text-center">
                    <small class="text-muted">Belum punya akun? Login saja dengan Google!</small>
                </div> --}}
            </div>
        </div>

        <!-- Elemen dekoratif lembut -->
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>
    </div>
    {{-- </div> --}}
@endsection

@push('styles')
    <style>
        /* Latar belakang lembut */
        .soft-hero {
            background: linear-gradient(180deg, #f7f9fc 0%, #f2f6fb 100%);
        }

        /* Kartu dengan sudut membulat dan bayangan halus */
        .card-soft {
            border-radius: 16px;
            border: 1px solid #eef2f7;
            box-shadow: 0 10px 30px rgba(16, 24, 40, 0.06);
        }

        /* Tipografi judul dan subjudul */
        .title {
            font-size: 1.375rem;
            /* h4-ish */
            font-weight: 700;
            color: #1f2937;
            letter-spacing: 0.2px;
        }

        .subtitle {
            font-size: 0.95rem;
        }

        /* Bungkus logo dengan latar gradient lembut */
        .logo-wrap {
            width: 72px;
            height: 72px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #eef2ff 0%, #e6f2ff 100%);
            margin: 0 auto;
        }

        /* Tombol Google versi soft (putih, border halus) */
        .btn-google-soft {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 16px;
            background: #ffffff;
            color: #111827;
            font-weight: 600;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            transition: all .2s ease;
            text-decoration: none;
        }

        .btn-google-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(16, 24, 40, 0.08);
            border-color: #d1d5db;
        }

        .btn-google-soft i {
            color: #ea4335;
            /* warna logo Google */
        }

        /* Dekorasi blob lembut */
        .bg-blob {
            position: absolute;
            filter: blur(40px);
            opacity: .35;
            z-index: 0;
            pointer-events: none;
        }

        .bg-blob-1 {
            width: 280px;
            height: 280px;
            background: #dbeafe;
            /* biru muda */
            top: -40px;
            right: -40px;
            border-radius: 50%;
        }

        .bg-blob-2 {
            width: 240px;
            height: 240px;
            background: #fde68a;
            /* kuning lembut */
            bottom: -40px;
            left: -40px;
            border-radius: 50%;
        }

        /* Pastikan konten di atas dekorasi */
        .card-soft .card-body {
            position: relative;
            z-index: 1;
        }

        /* Dark mode opsional (jika sistem pengguna gelap) */
        @media (prefers-color-scheme: dark) {
            .soft-hero {
                background: linear-gradient(180deg, #0b1220 0%, #111827 100%);
            }

            .card-soft {
                background: #0f172a;
                border-color: #1f2a3a;
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4);
            }

            .title {
                color: #e5e7eb;
            }

            .subtitle,
            .text-muted {
                color: #9ca3af !important;
            }

            .btn-google-soft {
                background: #0b1220;
                color: #e5e7eb;
                border-color: #1f2a3a;
            }

            .btn-google-soft:hover {
                border-color: #334155;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.45);
            }

            .bg-blob-1 {
                background: #1e293b;
            }

            .bg-blob-2 {
                background: #3b82f6;
                opacity: .25;
            }
        }
    </style>
@endpush

@section('scripts')
    <!-- Font Awesome untuk logo Google (jika belum ada) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
