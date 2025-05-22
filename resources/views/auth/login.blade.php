@extends('layouts.app')

@section('content')
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: #f9fafc;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
        }


        .login-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .login-left {
            flex: 1;
            background: url('{{ asset('assets/img/avatars/admin.webp') }}') no-repeat center center;
            background-size: cover;
        }

        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #ffffff;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 1rem 0;
        }

        .login-form-wrapper img {
            width: 200px;
            margin-bottom: 10px;
        }

        .login-form-wrapper h4 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #222;
        }

        .login-form-wrapper p {
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.95rem;
            color: #333;
        }

        .underline-input {
            border: none;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            background: #f5f9ff;
            font-size: 1rem;
            padding: 0.5rem;
            box-shadow: none;
            transition: all 0.3s ease-in-out;
        }

        .underline-input:focus {
            border-bottom-color: #00326e;
            outline: none;
            background: #eef3ff;
        }

        .input-group-text {
            background: transparent;
            border: none;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            padding-left: 0;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: #444;
        }

        .btn-primary {
            background-color: #00326e;
            border-color: #00326e;
            padding: 0.6rem;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 6px;
            margin-top: 0.75rem;
        }

        .btn-primary:hover {
            background-color: #00224d;
        }

        .forgot-link {
            font-size: 0.88rem;
            color: #0056b3;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .password-input {
            padding-right: 2.5rem;
            background-color: #eef4ff;
            /* or your preferred shade */
            border: none;
            border-bottom: 2px solid #ccc;
            border-radius: 0;
            box-shadow: none;
            font-size: 1rem;
            transition: all 0.3s ease-in-out;
        }

        .password-input:focus {
            border-bottom-color: #00326e;
            background-color: #e4edff;
            outline: none;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
        }

        .password-toggle:hover {
            color: #00326e;
        }

        .corner-banner {
            position: absolute;
            top: 20px;
            left: -60px;
            width: 200px;
            background: linear-gradient(90deg,
                    red, orange, yellow, green, cyan, blue, violet);
            background-size: 400% 100%;
            animation: rainbowSlide 5s linear infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
            padding: 6px 0;
            transform: rotate(-45deg);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        @keyframes rainbowSlide {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 100% 50%;
            }
        }

        /* make sure .login-left allows positioning */
        .login-left {
            position: relative;
            overflow: hidden;
        }

        .underline-input:focus,
        .password-input:focus {
            border-bottom-color: #00326e;
            outline: none !important;
            box-shadow: none !important;
            background-color: #eef3ff;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                height: 220px;
            }

            .login-right {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-left">
            <div class="corner-banner">Vinay Files</div>
        </div>
        <div class="login-right">
            <div class="text-center login-form-wrapper">
                <img src="{{ asset('assets/img/avatars/logo-4.png') }}" alt="Logo">
                <h4>Welcome Back, Vinay</h4>
                <p>Please sign in to continue</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-4 text-start">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email"
                            class="form-control underline-input @error('email') is-invalid @enderror"
                            placeholder="admin@example.com" required autofocus />
                        @error('email')
                            <div class="mt-1 text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4 text-start position-relative">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password"
                                class="form-control underline-input password-input @error('password') is-invalid @enderror"
                                placeholder="Enter password" required />
                            <span class="password-toggle" id="toggle-password" style="cursor: pointer;">
                                <i class="bx bx-hide" id="toggle-password-icon"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="mt-1 text-danger small">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Remember -->
                    <div class="mb-3 form-check text-start">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" />
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <!-- Forgot & Submit -->
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        @endif
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.getElementById("toggle-password");
            const passwordInput = document.getElementById("password");
            const icon = document.getElementById("toggle-password-icon");

            togglePassword.addEventListener("click", function() {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);
                icon.classList.toggle("bx-show");
                icon.classList.toggle("bx-hide");
            });
        });
    </script>
@endsection
