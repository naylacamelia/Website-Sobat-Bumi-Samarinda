<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('title', 'Admin') — Sobat Bumi Samarinda</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

  @stack('styles')
</head>

<body>
@php
  $adminIcons = [
    'leaf' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19.5 4.5c-5.7.2-9.9 1.8-12.4 4.4-2.3 2.3-2.6 5.8-.7 7.8 2 2 5.5 1.6 7.8-.7 2.5-2.5 4.1-6.7 4.4-12.4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M8 16 14 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

    'dashboard' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h7v7H4V4Zm9 0h7v7h-7V4ZM4 13h7v7H4v-7Zm9 0h7v7h-7v-7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>',

    'article' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 4h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

    'plus' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.3" stroke-linecap="round"/></svg>',

    'external' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M14 5h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="m13 11 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 14v3.5A1.5 1.5 0 0 1 17.5 19h-11A1.5 1.5 0 0 1 5 17.5v-11A1.5 1.5 0 0 1 6.5 5H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

    'logout' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 6H6.5A1.5 1.5 0 0 0 5 7.5v9A1.5 1.5 0 0 0 6.5 18H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 8l4 4-4 4M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

    'menu' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 7h14M5 12h14M5 17h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

    'search' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m16.5 16.5 3.5 3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',

    'gallery' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 5h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2"/><path d="m4 16 4.5-4.5 3.5 3.5 2-2L20 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',

    'testimonial' => '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 17.5c-1.7-1.4-2.7-3-2.7-5.2C4.3 8 7.8 5 12 5s7.7 3 7.7 7.3S16.2 19.5 12 19.5c-.8 0-1.7-.1-2.4-.4L6 20l1-2.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 11.5h6M9 14.5h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
  ];
@endphp

<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi admin">
    <div class="sidebar-brand">
      <span class="sidebar-brand-mark">{!! $adminIcons['leaf'] !!}</span>

      <div>
        <strong>Admin Panel</strong>
        <span>Sobat Bumi Samarinda</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a
        href="{{ route('admin.dashboard') }}"
        class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
      >
        <span class="nav-icon">{!! $adminIcons['dashboard'] !!}</span>
        <span>Dashboard</span>
      </a>

      <a
        href="{{ route('admin.berita') }}"
        class="sidebar-nav-item {{ request()->routeIs('admin.berita') ? 'active' : '' }}"
      >
        <span class="nav-icon">{!! $adminIcons['article'] !!}</span>
        <span>Kelola Berita</span>
      </a>

      <a
        href="{{ route('admin.tambah-berita') }}"
        class="sidebar-nav-item {{ request()->routeIs('admin.tambah-berita') || request()->routeIs('admin.edit-berita') ? 'active' : '' }}"
      >
        <span class="nav-icon">{!! $adminIcons['plus'] !!}</span>
        <span>Tambah Berita</span>
      </a>

      <a
        href="{{ route('admin.galeri') }}"
        class="sidebar-nav-item {{ request()->routeIs('admin.galeri') || request()->routeIs('admin.tambah-galeri') || request()->routeIs('admin.edit-galeri') ? 'active' : '' }}"
      >
        <span class="nav-icon">{!! $adminIcons['gallery'] !!}</span>
        <span>Galeri</span>
      </a>

      <a
        href="{{ route('admin.testimoni') }}"
        class="sidebar-nav-item {{ request()->routeIs('admin.testimoni') || request()->routeIs('admin.tambah-testimoni') || request()->routeIs('admin.edit-testimoni') ? 'active' : '' }}"
      >
        <span class="nav-icon">{!! $adminIcons['testimonial'] !!}</span>
        <span>Testimoni</span>
      </a>

      <div class="sidebar-nav-spacer"></div>

      <a
        href="{{ route('home') }}"
        rel="noopener"
        class="sidebar-nav-item"
      >
        <span class="nav-icon">{!! $adminIcons['external'] !!}</span>
        <span>Lihat Website</span>
      </a>
    </nav>

    <div class="sidebar-user">
      <div class="sidebar-user-avatar">A</div>

      <div class="sidebar-user-copy">
        <strong>Admin</strong>
        <span>Super Admin</span>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.logout') }}" class="sidebar-logout-form">
      @csrf

      <button type="submit" class="sidebar-logout">
        <span class="nav-icon">{!! $adminIcons['logout'] !!}</span>
        <span>Logout</span>
      </button>
    </form>
  </aside>

  <button
    type="button"
    class="admin-sidebar-overlay"
    id="adminSidebarOverlay"
    data-sidebar-overlay
    aria-label="Tutup sidebar"
  ></button>

  <div class="admin-main">
    <header class="admin-topbar">
      <button
        type="button"
        class="admin-menu-btn"
        id="adminMenuBtn"
        data-sidebar-toggle
        aria-label="Buka menu admin"
        aria-controls="adminSidebar"
        aria-expanded="false"
      >
        {!! $adminIcons['menu'] !!}
      </button>

      <div class="topbar-actions">
        <time class="topbar-date" id="currentDate"></time>

        <div class="topbar-avatar" title="Admin">A</div>
      </div>
    </header>

    <main class="admin-content">
      @yield('content')
    </main>
  </div>
</div>

<script src="{{ asset('assets/js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>
