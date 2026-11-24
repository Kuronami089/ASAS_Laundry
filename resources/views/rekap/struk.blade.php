@php use App\Support\FormatHelper; @endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Struk {{ $trx->kode_transaksi }} | ASAS Laundry</title>
  <meta name="description" content="Struk resmi transaksi laundry {{ $trx->kode_transaksi }} atas nama {{ $trx->nama_pelanggan }}.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&family=Lora:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
  <main class="receipt-page">
    <div class="receipt-shell">
      <div class="receipt-toolbar">
        <a class="btn btn--quiet" href="{{ route('rekap.index') }}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
          <span>Kembali ke rekap</span>
        </a>
        <button type="button" class="btn btn--primary" id="print-receipt-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="6 9 6 2 18 2 18 9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
          </svg>
          <span>Cetak struk</span>
        </button>
      </div>

      <article class="receipt-card" aria-labelledby="receipt-title">
        <header class="receipt-head">
          <div class="brand-mark">
            <span class="brand-mark__seal" aria-hidden="true">
              <img class="brand-mark__logo" src="{{ asset('assets/img/logo-asas-mark.svg') }}" width="284" height="202" alt="">
            </span>
            <span class="brand-mark__text">
              <h1 class="brand-mark__name" id="receipt-title">ASAS Laundry</h1>
              <span class="brand-mark__sub">Struk Resmi Transaksi Cucian</span>
            </span>
          </div>

          @if ($trx->status === 'Selesai')
            <span class="badge badge--selesai">Selesai</span>
          @else
            <span class="badge badge--proses">Proses</span>
          @endif
        </header>

        <hr class="receipt-divider">

        <dl class="receipt-list">
          <div class="receipt-row">
            <dt>Kode transaksi</dt>
            <dd class="mono">{{ $trx->kode_transaksi }}</dd>
          </div>
          <div class="receipt-row">
            <dt>Tanggal masuk</dt>
            <dd class="mono">{{ FormatHelper::tanggal($trx->tanggal_masuk) }}</dd>
          </div>
          @if ($trx->tanggal_selesai)
            <div class="receipt-row">
              <dt>Tanggal selesai</dt>
              <dd class="mono">{{ FormatHelper::tanggal($trx->tanggal_selesai) }}</dd>
            </div>
          @endif
          <div class="receipt-row">
            <dt>Petugas</dt>
            <dd>{{ $trx->petugas?->nama_lengkap ?? '-' }}</dd>
          </div>
        </dl>

        <hr class="receipt-divider">

        <dl class="receipt-list">
          <div class="receipt-row">
            <dt>Nama pelanggan</dt>
            <dd>{{ $trx->nama_pelanggan }}</dd>
          </div>
          <div class="receipt-row">
            <dt>Nomor HP</dt>
            <dd class="mono">{{ $trx->no_hp }}</dd>
          </div>
        </dl>

        <hr class="receipt-divider">

        <dl class="receipt-list">
          <div class="receipt-row">
            <dt>Jenis layanan</dt>
            <dd>{{ $trx->layanan->nama_layanan }} ({{ $trx->layanan->estimasi_selesai }})</dd>
          </div>
          <div class="receipt-row">
            <dt>Berat cucian</dt>
            <dd class="mono">{{ FormatHelper::berat($trx->berat_kg) }}</dd>
          </div>
          <div class="receipt-row">
            <dt>Tarif per kg</dt>
            <dd class="mono">{{ FormatHelper::rupiah($trx->harga_saat_transaksi) }}</dd>
          </div>
        </dl>

        <hr class="receipt-divider">

        <div class="receipt-total">
          <span>Total biaya</span>
          <span class="receipt-total__amount">{{ FormatHelper::rupiah($trx->total_harga) }}</span>
        </div>

        <p class="receipt-note">
          Simpan struk ini sebagai bukti pengambilan cucian di ASAS Laundry. Terima kasih atas kepercayaan Anda.
        </p>
      </article>
    </div>
  </main>

  <script src="{{ asset('assets/js/app.js') }}" defer></script>
</body>
</html>
