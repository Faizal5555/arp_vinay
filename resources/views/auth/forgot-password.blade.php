@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.06);
            padding: 30px;
        }

        .logo {
            display: block;
            margin: 0 auto 20px auto;
            max-height: 80px;
        }

        .form-title {
            font-weight: 600;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #00326e;
        }
    </style>
    </head>

    <body>
        <div class="auth-wrapper">
            <div class="auth-card">
                <img src="{{ asset('assets/img/avatars/logo-4.png') }}" alt="Logo" class="logo">

                <h2 class="form-title">Forgot Password</h2>

                @if (session('status'))
                    <div class="text-center alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Enter your email address</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" required autofocus>

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    @endsection
