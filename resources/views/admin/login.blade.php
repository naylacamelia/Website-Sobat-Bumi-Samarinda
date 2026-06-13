<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sobat Bumi Samarinda</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <div class="admin-login-page">
        <div class="admin-login-shell">
            <div class="admin-login-card">
                <div class="admin-login-brand">
    <div class="admin-login-logo">
        <img 
            src="{{ asset('assets/images/logo.png') }}" 
            alt="Logo Sobat Bumi Samarinda"
        >
    </div>
</div>

                <div class="admin-login-heading">
                    <h2>Selamat Datang</h2>
                    <p>Silakan Masukkan Email dan Password Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="admin-login-alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.login.post') }}" method="POST" class="admin-login-form">
                    @csrf

                    <div class="admin-form-group">
                        <label for="email">Email</label>
                        <div class="admin-input-wrap">
                            <span class="admin-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                    <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                autocomplete="email"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="password">Password</label>
                        <div class="admin-input-wrap admin-password-wrap">
                            <span class="admin-input-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <path d="M6.5 11h11A1.5 1.5 0 0 1 19 12.5v6A1.5 1.5 0 0 1 17.5 20h-11A1.5 1.5 0 0 1 5 18.5v-6A1.5 1.5 0 0 1 6.5 11Z" stroke="currentColor" stroke-width="2" />
                                </svg>
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="admin-password-toggle"
                                id="passwordToggle"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                                </svg>

                                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 3l18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    <path d="M10.7 5.2A10.4 10.4 0 0 1 12 5c6 0 9.5 7 9.5 7a17.8 17.8 0 0 1-2.4 3.3M6.2 6.9C3.8 8.7 2.5 12 2.5 12s3.5 7 9.5 7c1.8 0 3.3-.5 4.6-1.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.9 9.9A3 3 0 0 0 14.1 14.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                    </div>

                  
                    <button type="submit" class="admin-login-submit">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('passwordToggle');

            if (!passwordInput || !toggleButton) return;

            toggleButton.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';

                passwordInput.type = isPassword ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(isPassword));
                toggleButton.setAttribute(
                    'aria-label',
                    isPassword ? 'Sembunyikan password' : 'Tampilkan password'
                );

                toggleButton.classList.toggle('is-visible', isPassword);
            });
        });
    </script>
</body>
</html>