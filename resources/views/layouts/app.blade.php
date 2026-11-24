@php
    $user      = auth()->user();
    $roleLabel = $user && $user->isAdmin() ? 'Admin' : 'Kasir';

    $navItems = [
        [
            'route' => 'dashboard',
            'label' => 'Dashboard',
            'icon'  => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
            'admin' => false,
        ],
        [
            'route' => 'layanan.index',
            'label' => 'Data Layanan',
            'icon'  => '<path d="M20.59 13.41 13.42 20.58a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><circle cx="7" cy="7" r="1.25"/>',
            'admin' => true,
        ],
        [
            'route' => 'transaksi.index',
            'label' => 'Transaksi Laundry',
            'icon'  => '<path d="M12 5v14"/><path d="M5 12h14"/><rect x="3" y="3" width="18" height="18" rx="3"/>',
            'admin' => false,
        ],
        [
            'route' => 'rekap.index',
            'label' => 'Rekap Transaksi Laundry',
            'icon'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>',
            'admin' => false,
        ],
    ];

    $visibleMenus = collect($navItems)->filter(
        fn (array $item) => !$item['admin'] || ($user && $user->isAdmin())
    );
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'ASAS Laundry') | ASAS Laundry</title>
  <meta name="description" content="@yield('description', 'Sistem Informasi Manajemen Laundry berbasis Laravel untuk Admin dan Kasir.')">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
  <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

  <!-- Drawer navigasi mobile (< 60rem) -->
  <dialog class="Drawer" id="mobile-drawer" aria-label="Menu navigasi">
    <div class="Drawer-scroller">
      <nav class="Drawer-sheet" aria-label="Navigasi mobile">
        <div class="Drawer-header">
          <a class="brand-mark" href="{{ route('dashboard') }}" aria-label="Beranda ASAS Laundry">
            <span class="brand-mark__seal" aria-hidden="true">
              <img class="brand-mark__logo" src="{{ asset('assets/img/logo-asas-mark.svg') }}" width="284" height="202" alt="">
            </span>
            <span class="brand-mark__text">
              <span class="brand-mark__name">ASAS Laundry</span>
            </span>
          </a>
          <button type="button" class="btn btn--quiet btn--icon" id="drawer-close" aria-label="Tutup menu" autofocus>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        <ul class="nav-list">
          @foreach ($visibleMenus as $item)
            <li>
              <a class="nav-link" href="{{ route($item['route']) }}" @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                <span>{{ $item['label'] }}</span>
              </a>
            </li>
          @endforeach
        </ul>

        <div class="sidebar__footer">
          <div class="user-meta">
            <span class="user-meta__name">{{ $user?->nama_lengkap }}</span>
            <span class="user-meta__role">Role {{ $roleLabel }}</span>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn--quiet btn--full">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              <span>Keluar</span>
            </button>
          </form>
        </div>
      </nav>
    </div>
  </dialog>

  <div class="app-shell">
    <!-- Sidebar navigasi desktop -->
    <aside class="sidebar" aria-label="Navigasi utama">
      <a class="brand-mark" href="{{ route('dashboard') }}" aria-label="Beranda ASAS Laundry">
        <span class="brand-mark__seal" aria-hidden="true">
          <img class="brand-mark__logo" src="{{ asset('assets/img/logo-asas-mark.svg') }}" width="284" height="202" alt="">
        </span>
        <span class="brand-mark__text">
          <span class="brand-mark__name">ASAS Laundry</span>
          <span class="brand-mark__sub">Manajemen Operasional</span>
        </span>
      </a>

      <nav aria-label="Menu aplikasi">
        <ul class="nav-list">
          @foreach ($visibleMenus as $item)
            <li>
              <a class="nav-link" href="{{ route($item['route']) }}" @if (request()->routeIs($item['route'])) aria-current="page" @endif>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $item['icon'] !!}</svg>
                <span>{{ $item['label'] }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      </nav>

      <div class="sidebar__footer">
        <div class="user-meta">
          <span class="user-meta__name">{{ $user?->nama_lengkap }}</span>
          <span class="user-meta__role">Role {{ $roleLabel }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn--quiet btn--full">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Keluar</span>
          </button>
        </form>
      </div>
    </aside>

    <!-- Konten utama -->
    <div class="app-main">
      <header class="mobile-topbar">
        <button
          type="button"
          class="btn btn--quiet btn--icon"
          id="drawer-open"
          aria-label="Buka menu navigasi"
          aria-expanded="false"
          aria-controls="mobile-drawer"
        >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>

        <a class="brand-mark" href="{{ route('dashboard') }}" aria-label="Beranda ASAS Laundry">
          <span class="brand-mark__name">ASAS Laundry</span>
        </a>

        <span class="user-meta__role">{{ $roleLabel }}</span>
      </header>

      <main class="page-content" id="main-content" tabindex="-1">
        @if (session('flash_message'))
          @php
            $flashType  = session('flash_type', 'success');
            $alertClass = match ($flashType) {
                'success' => 'alert--success',
                'warning' => 'alert--warning',
                default   => 'alert--error',
            };
          @endphp
          <div class="alert {{ $alertClass }}" role="status" aria-live="polite">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              @if ($flashType === 'success')
                <circle cx="12" cy="12" r="10"/>
                <polyline points="9 12 11.5 14.5 16 9.5"/>
              @else
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
              @endif
            </svg>
            <span>{{ session('flash_message') }}</span>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <script src="{{ asset('assets/js/app.js') }}" defer></script>
</body>
</html>
