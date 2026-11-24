<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk | ASAS Laundry</title>
  <meta name="description" content="Halaman login Admin dan Kasir untuk Sistem Informasi Manajemen Laundry ASAS Laundry.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
  <main class="auth-page">
    <!-- Latar belakang foto studio full-screen menyatu di belakang seluruh layar -->
    <div class="auth-bg" aria-hidden="true">
      <img
        class="auth-bg__photo"
        src="{{ asset('assets/img/login-laundry.jpg') }}"
        width="896"
        height="1200"
        alt=""
        fetchpriority="high"
      >
      <div class="auth-bg__scrim"></div>
    </div>

    <!-- Sisi kiri: area visual terbuka dengan tipografi organik + efek Black Ink Bleed -->
    <section class="auth-showcase" aria-label="Gambaran layanan ASAS Laundry">
      <div class="auth-showcase__content">
        <div class="ink-bleed-block">
          <p class="auth-showcase__headline ink-bleed" data-ink-bleed aria-label="Cucian rapi, setiap helai terjaga.">
            <span class="ink-bleed__line">
              <span class="ink-bleed__word" data-ink-word>Cucian</span>
              <span class="ink-bleed__word" data-ink-word>rapi,</span>
            </span>
            <span class="ink-bleed__line">
              <span class="ink-bleed__word" data-ink-word>setiap</span>
              <span class="ink-bleed__word" data-ink-word>helai</span>
              <span class="ink-bleed__word" data-ink-word>terjaga.</span>
            </span>
          </p>

          <svg class="scribble-underline" viewBox="0 0 240 16" fill="none" aria-hidden="true">
            <path class="scribble-underline__path" d="M4 11 C58 4, 118 14, 182 7 C206 5, 224 8, 236 6"/>
          </svg>
        </div>

        <p class="auth-showcase__sub">
          Kelola pesanan harian, status pengerjaan, dan laporan keuangan laundry dalam satu ruang kerja yang tenang.
        </p>
      </div>
    </section>

    <!-- Sisi kanan: panel login kaca buram di atas foto full-screen, dipisah garis vertikal -->
    <section class="auth-panel" aria-labelledby="login-heading">
      <div class="auth-panel__inner">
        <div class="auth-brand">
          <img
            class="auth-brand__logo"
            src="{{ asset('assets/img/logo-asas-laundry.svg') }}"
            width="360"
            height="272"
            alt="Logo ASAS Laundry"
          >
          <div class="auth-brand__copy">
            <h1 id="login-heading">Masuk ke aplikasi</h1>
            <p class="text-muted">Gunakan akun Admin atau Kasir untuk membuka dashboard operasional.</p>
          </div>
        </div>

        @if ($errors->has('login'))
          <div class="alert alert--error" role="alert">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $errors->first('login') }}</span>
          </div>
        @elseif (session('flash_message'))
          <div class="alert {{ session('flash_type') === 'success' ? 'alert--success' : 'alert--error' }}" role="status" aria-live="polite">
            <span>{{ session('flash_message') }}</span>
          </div>
        @endif

        <form class="form-stack" method="POST" action="{{ route('login.submit') }}" aria-labelledby="login-heading">
          @csrf

          <div class="field">
            <label class="field__label" for="username">
              Username <span class="field__req">(wajib)</span>
            </label>
            <div class="input-icon-wrap">
              <input
                class="input"
                type="text"
                id="username"
                name="username"
                value="{{ old('username') }}"
                placeholder="Masukkan username"
                autocomplete="username"
                autocorrect="off"
                autocapitalize="none"
                spellcheck="false"
                enterkeyhint="next"
                data-error="username-error"
                @error('username') aria-invalid="true" @enderror
                required
              >
              <svg class="input-icon-wrap__glyph" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
            <span class="field__error @error('username') is-visible @enderror" id="username-error">
              {{ $errors->first('username') ?: 'Masukkan username akun Admin atau Kasir.' }}
            </span>
          </div>

          <div class="field">
            <label class="field__label" for="password">
              Kata sandi <span class="field__req">(wajib)</span>
            </label>
            <div class="password-wrap">
              <input
                class="input"
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan kata sandi"
                autocomplete="current-password"
                enterkeyhint="done"
                data-error="password-error"
                @error('password') aria-invalid="true" @enderror
                required
              >
              <button
                type="button"
                class="password-toggle"
                id="toggle-password"
                aria-pressed="false"
                aria-label="Tampilkan kata sandi"
              >
                Tampilkan
              </button>
            </div>
            <span class="field__error @error('password') is-visible @enderror" id="password-error">
              {{ $errors->first('password') ?: 'Masukkan kata sandi untuk melanjutkan.' }}
            </span>
          </div>

          <button type="submit" class="btn btn--primary btn--lg btn--full">Masuk</button>
        </form>

        <div class="demo-accounts">
          <p class="demo-accounts__title">Akun pengujian lokal (klik untuk mengisi form)</p>
          <div class="demo-accounts__grid">
            <button type="button" class="demo-btn" data-demo-user="admin" data-demo-pass="admin123">
              <strong>Admin (Faqih Rachman)</strong>
              <span class="mono">admin / admin123</span>
            </button>
            <button type="button" class="demo-btn" data-demo-user="kasir" data-demo-pass="kasir123">
              <strong>Kasir (Nadia Putri)</strong>
              <span class="mono">kasir / kasir123</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </main>

  <script src="{{ asset('assets/js/app.js') }}" defer></script>
</body>
</html>
