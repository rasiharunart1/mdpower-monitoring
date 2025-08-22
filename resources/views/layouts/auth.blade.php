<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Login - MD PowerPlant')</title>
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
            /* needed for the blur overlay layering */
        }

        /* Blurred background overlay (keeps content sharp) */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            /* don't block clicks */
            /* Blur and slightly brighten/saturate the backdrop (the gradient/image set on body) */
            -webkit-backdrop-filter: blur(14px) saturate(120%);
            backdrop-filter: blur(14px) saturate(120%);
            /* Optional frosted tint over the blurred backdrop */
            background: rgba(255, 255, 255, 0.18);
        }

        .container {
            width: 100%;
            position: relative;
            /* make sure content sits above the blur overlay */
            z-index: 1;
        }

        .card {
            margin: 0 auto;
            max-width: 1000px;
        }

        .tractor-side {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            padding: 2rem;
            height: 100%;
            min-height: 400px;
        }

        .gradient-icon {
            font-size: 8rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #4e73df, #36b9cc, #1cc88a);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientAnimation 5s ease infinite;
            background-size: 200% 200%;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .brand-text {
            font-size: 1.5rem;
            background: linear-gradient(45deg, #4e73df, #36b9cc, #1cc88a);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
            text-align: center;
        }

        .login-form-container {
            display: flex;
            align-items: center;
            min-height: 400px;
        }

        .text-danger {
            color: #e74a3b !important;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .forgot-password-link {
            color: #5a5c69;
            text-decoration: underline;
            font-size: 0.875rem;
        }

        .forgot-password-link:hover {
            color: #3a3b45;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .tractor-side {
                display: none;
            }

            body {
                padding: 1rem;
            }

            .card {
                margin: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gradient-primary">
    <div class="container">
        @yield('content')
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
