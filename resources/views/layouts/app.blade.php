<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sobat Bumi Samarinda')</title>
    <meta name="description" content="@yield('meta_desc', 'Komunitas pelajar peduli lingkungan di Samarinda')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>

<body>

<header class="sb-navbar" id="siteNavbar">
    <div class="sb-navbar-shell">
        <a href="{{ route('home') }}" class="sb-navbar-brand" aria-label="Sobat Bumi Samarinda">
            <span class="sb-navbar-logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo Sobat Bumi Samarinda">
            </span>

            <span>Sobat Bumi Samarinda</span>
        </a>

        <button
            type="button"
            class="sb-navbar-toggle"
            id="navbarToggle"
            aria-label="Buka menu navigasi"
            aria-expanded="false"
            aria-controls="navbarMenu"
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="sb-navbar-menu" id="navbarMenu" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                Beranda
            </a>

            <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'active' : '' }}">
                Tentang Kami
            </a>

            <a href="{{ route('berita') }}" class="{{ request()->routeIs('berita') || request()->routeIs('berita.*') ? 'active' : '' }}">
                Berita
            </a>
        </nav>
@if (\Illuminate\Support\Facades\Auth::guard('admin')->check())
    <a href="{{ route('admin.dashboard') }}" class="sb-navbar-login">
        <span class="sb-navbar-login-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <rect x="4" y="4" width="16" height="16" rx="3.5"
                    stroke="currentColor"
                    stroke-width="2"
                />
                <path
                    d="M8 9h8M8 13h5M8 17h7"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>
        </span>
        <span>Dashboard</span>
    </a>
@else
    <a href="{{ route('admin.login') }}" class="sb-navbar-login">
        <span class="sb-navbar-login-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path
                    d="M7 11V8a5 5 0 0 1 10 0v3"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
                <rect x="5" y="11" width="14" height="9" rx="2"
                    stroke="currentColor"
                    stroke-width="2"
                />
                <path
                    d="M12 15v1.8"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>
        </span>
        <span>Login</span>
    </a>
@endif
    </div>
</header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand-name">
                        <span class="footer-brand-icon" aria-hidden="true">
                            <svg class="sb-icon-lg" viewBox="0 0 24 24" fill="none">
                                <path d="M5 19c7.5 0 13-5.5 14-14-8.5 1-14 6.5-14 14Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="M5 19c2.8-4.6 6.2-7.8 11-10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </span>
                        Sobat Bumi Samarinda
                    </div>
                    <p class="footer-desc">Komunitas pelajar yang bergerak bersama untuk Bumi yang lebih baik. Fokus pada lingkungan, pemberdayaan, dan energi bersih.</p>
<div class="footer-socials">
    <a 
        href="https://www.instagram.com/sobatbumi_samarinda/"
        class="footer-social-btn" 
        aria-label="Instagram Sobat Bumi Samarinda"
        target="_blank"
        rel="noopener noreferrer"
    >
        <svg class="sb-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4" y="4" width="16" height="16" rx="5" stroke="currentColor" stroke-width="2" />
            <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2" />
            <circle cx="17" cy="7" r="1" fill="currentColor" />
        </svg>
    </a>

    <a 
        href="https://www.youtube.com/@SobatBumiUNMUL"
        class="footer-social-btn" 
        aria-label="YouTube Sobat Bumi Samarinda"
        target="_blank"
        rel="noopener noreferrer"
    >
        <svg class="sb-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M21 12s0-4-1-5.3c-.3-.8-.9-1.4-1.7-1.6C16.8 4.7 12 4.7 12 4.7s-4.8 0-6.3.4c-.8.2-1.4.8-1.7 1.6C3 8 3 12 3 12s0 4 1 5.3c.3.8.9 1.4 1.7 1.6 1.5.4 6.3.4 6.3.4s4.8 0 6.3-.4c.8-.2 1.4-.8 1.7-1.6C21 16 21 12 21 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
            <path d="m10.5 9 4 3-4 3V9Z" fill="currentColor" />
        </svg>
    </a>


</div>
                </div>

                <div>
                    <p class="footer-heading">Navigasi</p>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        <li><a href="{{ route('tentang') }}">Tentang Kami</a></li>
                        <li><a href="{{ route('berita') }}">Berita</a></li>
                        <li><a href="{{ route('kegiatan') }}">Kegiatan</a></li>
                        <li><a href="{{ route('kontak') }}">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <p class="footer-heading">Fokus Gerakan</p>
                    <ul class="footer-links">
                        <li><a href="#">Lingkungan Hidup</a></li>
                        <li><a href="#">Pemberdayaan Masyarakat</a></li>
                        <li><a href="#">Energi Bersih</a></li>
                        <li><a href="#">Edukasi Lingkungan</a></li>
                        <li><a href="#">Relawan & Donasi</a></li>
                    </ul>
                </div>

                <div>
                    <p class="footer-heading">Kontak Kami</p>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true">
                            <svg class="sb-icon" viewBox="0 0 24 24" fill="none">
                                <path d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12Z" stroke="currentColor" stroke-width="2" />
                                <circle cx="12" cy="9" r="2.4" stroke="currentColor" stroke-width="2" />
                            </svg>
                        </span>
                        <span>Jl. Kuaro, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75117
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true">
                            <svg class="sb-icon" viewBox="0 0 24 24" fill="none">
                                <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>sobatbumisamarinda@gmail.com
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon" aria-hidden="true">
                            <svg class="sb-icon" viewBox="0 0 24 24" fill="none">
                                <path d="M7 5 5.5 6.5c-.8.8-.7 2.2.1 3.8a18 18 0 0 0 8.1 8.1c1.6.8 3 .9 3.8.1L19 17l-3.2-3.2-1.5 1.5c-1.6-.8-3-2.2-3.8-3.8L12 10 7 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>0858 4594 1522
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom-wrap">
            <div class="container">
                <div class="footer-bottom">
                    <span>© 2026 obat Bumi Samarinda. Semua hak dilindungi.</span>
                    <span>Dibuat untuk gerakan lingkungan yang lebih baik.</span>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
    @stack('scripts')
</body>

</html>