@extends('layouts.app')

@section('title', 'Rekap Transaksi Laundry')
@section('description', 'Laporan transaksi laundry relasional (SQL JOIN) terkelompok rapi per status, layanan, tanggal, atau halaman lengkap dengan cetak struk.')

@section('content')
@php
    use App\Support\FormatHelper;

    $groupOptions = [
        'status' => [
            'label' => 'Per status',
            'icon'  => '<circle cx="12" cy="12" r="9"/><polyline points="9 12 11.2 14.2 15.5 9.8"/>',
        ],
        'layanan' => [
            'label' => 'Per layanan',
            'icon'  => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 12 12 17 22 12"/><polyline points="2 17 12 22 22 17"/>',
        ],
        'tanggal' => [
            'label' => 'Per tanggal',
            'icon'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        ],
        'halaman' => [
            'label' => 'Per halaman (5)',
            'icon'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        ],
    ];

    $baseQuery = array_filter([
        'q'          => $keyword,
        'status'     => $status !== 'semua' ? $status : null,
        'layanan_id' => $layananId > 0 ? $layananId : null,
    ], static fn ($v) => $v !== null && $v !== '');
@endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Rekap transaksi laundry</h1>
    <p class="page-header__desc">
      Laporan keuangan dan riwayat order yang dikelompokkan rapi agar mudah dipindai tanpa scroll panjang ke bawah.
    </p>
  </div>
  <div class="page-header__actions">
    <a class="btn btn--primary" href="{{ route('transaksi.index') }}">Catat transaksi baru</a>
    <button type="button" class="btn btn--quiet" id="print-report-btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <polyline points="6 9 6 2 18 2 18 9"/>
        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
        <rect x="6" y="14" width="12" height="8"/>
      </svg>
      <span>Cetak laporan</span>
    </button>
  </div>
</section>

<!-- Ringkasan Laporan Sesuai Filter Aktif -->
<section class="stat-strip" aria-label="Ringkasan rekap keuangan">
  <div class="stat-item">
    <span class="stat-item__label">Jumlah transaksi</span>
    <span class="stat-item__value">{{ $jumlahTransaksi }}</span>
    <span class="stat-item__note">{{ $hasActiveFilter ? 'Sesuai filter aktif' : 'Seluruh data tersimpan' }}</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Status Proses</span>
    <span class="stat-item__value">{{ $jumlahProses }}</span>
    <span class="stat-item__note">Dalam pengerjaan</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Status Selesai</span>
    <span class="stat-item__value">{{ $jumlahSelesai }}</span>
    <span class="stat-item__note">Cucian telah rampung</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Total pendapatan</span>
    <span class="stat-item__value stat-item__value--accent">{{ FormatHelper::rupiah($totalPendapatan) }}</span>
    <span class="stat-item__note">Akumulasi tabel rekap</span>
  </div>
</section>

<!-- Panel Filter Pencarian (<search>) -->
<section class="panel panel--well filter-panel" aria-label="Filter rekap transaksi">
  <search>
    <form class="filter-form" method="GET" action="{{ route('rekap.index') }}">
      <input type="hidden" name="group" value="{{ $groupBy }}">

      <div class="field">
        <label class="field__label" for="filter-q">Cari pelanggan atau kode</label>
        <input
          class="input"
          type="search"
          id="filter-q"
          name="q"
          value="{{ $keyword }}"
          placeholder="Nama pelanggan, no HP, atau LDR-0001"
          autocorrect="off"
          spellcheck="false"
          enterkeyhint="search"
        >
      </div>

      <div class="field">
        <label class="field__label" for="filter-status">Status pengerjaan</label>
        <select class="select" id="filter-status" name="status">
          <option value="semua" @selected($status === 'semua')>Semua status</option>
          <option value="Proses" @selected($status === 'Proses')>Proses</option>
          <option value="Selesai" @selected($status === 'Selesai')>Selesai</option>
        </select>
      </div>

      <div class="field">
        <label class="field__label" for="filter-layanan">Jenis layanan</label>
        <select class="select" id="filter-layanan" name="layanan_id">
          <option value="">Semua layanan</option>
          @foreach ($daftarLayanan as $item)
            <option value="{{ $item->id }}" @selected($layananId === $item->id)>
              {{ $item->nama_layanan }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn">Terapkan filter</button>
        @if ($hasActiveFilter)
          <a class="btn btn--quiet" href="{{ route('rekap.index', ['group' => $groupBy]) }}">Reset</a>
        @endif
      </div>
    </form>
  </search>
</section>

<!-- Floating Dock Ikon Mode Pengelompokan (hover untuk melebar & menampilkan label) -->
<div class="rekap-toolbar" aria-label="Pengaturan tampilan rekap">
  <nav class="group-dock" aria-label="Kelompokkan transaksi">
    @foreach ($groupOptions as $key => $opt)
      <a
        class="dock-item"
        href="{{ route('rekap.index', array_merge($baseQuery, ['group' => $key])) }}"
        aria-label="{{ $opt['label'] }}"
        @if ($groupBy === $key) aria-current="page" @endif
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $opt['icon'] !!}</svg>
        <span class="dock-item__label-wrap">
          <span class="dock-item__label"><span class="dock-item__label-inner">{{ $opt['label'] }}</span></span>
        </span>
      </a>
    @endforeach
  </nav>

  @if ($groupBy !== 'halaman' && $groupedRekap->isNotEmpty())
    <div class="group-controls">
      <button type="button" class="btn btn--quiet" id="expand-all-groups">Buka semua</button>
      <button type="button" class="btn btn--quiet" id="collapse-all-groups">Lipat semua</button>
    </div>
  @endif
</div>

@if ($rekapTransaksi->isEmpty())
  <section class="panel">
    <div class="empty-state">
      <p class="empty-state__title">
        {{ $hasActiveFilter ? 'Tidak ada transaksi yang cocok dengan filter' : 'Belum ada transaksi tercatat' }}
      </p>
      <p class="empty-state__desc">
        {{ $hasActiveFilter
            ? 'Ubah kata kunci pencarian atau reset filter untuk melihat seluruh data transaksi.'
            : 'Mulai catat order pertama pelanggan di halaman Transaksi Laundry.' }}
      </p>
      @if ($hasActiveFilter)
        <a class="btn btn--quiet" href="{{ route('rekap.index', ['group' => $groupBy]) }}">Reset filter</a>
      @else
        <a class="btn btn--quiet" href="{{ route('transaksi.index') }}">Catat transaksi baru</a>
      @endif
    </div>
  </section>
@elseif ($groupBy === 'halaman' && $paginatedRows)
  <!-- Mode Per Halaman (maksimal 5 transaksi per halaman) -->
  <section class="panel" aria-labelledby="tabel-rekap-heading">
    <div class="panel__head">
      <div>
        <h2 class="panel__title" id="tabel-rekap-heading">Daftar transaksi (halaman {{ $paginatedRows->currentPage() }} dari {{ $paginatedRows->lastPage() }})</h2>
        <p class="panel__meta">Menampilkan {{ $paginatedRows->firstItem() }}-{{ $paginatedRows->lastItem() }} dari {{ $paginatedRows->total() }} transaksi.</p>
      </div>
    </div>

    @include('rekap.partials.table', ['rows' => $paginatedRows, 'headingId' => 'tabel-rekap-heading'])

    @if ($paginatedRows->hasPages())
      <nav class="pager" aria-label="Navigasi halaman rekap">
        @if ($paginatedRows->onFirstPage())
          <span class="btn btn--quiet" aria-disabled="true">Sebelumnya</span>
        @else
          <a class="btn btn--quiet" href="{{ $paginatedRows->previousPageUrl() }}">Sebelumnya</a>
        @endif

        <div class="pager__pages">
          @for ($p = 1; $p <= $paginatedRows->lastPage(); $p++)
            <a
              class="pager__num"
              href="{{ $paginatedRows->url($p) }}"
              @if ($p === $paginatedRows->currentPage()) aria-current="page" @endif
            >
              {{ $p }}
            </a>
          @endfor
        </div>

        @if ($paginatedRows->hasMorePages())
          <a class="btn btn--quiet" href="{{ $paginatedRows->nextPageUrl() }}">Berikutnya</a>
        @else
          <span class="btn btn--quiet" aria-disabled="true">Berikutnya</span>
        @endif
      </nav>
    @endif
  </section>
@else
  <!-- Mode Per Grup (<details> accordion bersih dengan subtotal tiap grup) -->
  <div class="rekap-groups" data-rekap-groups>
    @foreach ($groupedRekap as $index => $group)
      @php $groupHeadingId = 'rekap-group-' . $index; @endphp
      <details class="rekap-group" @if ($group['open']) open @endif>
        <summary class="rekap-group__summary">
          <div class="rekap-group__left">
            <svg class="rekap-group__chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
            <h2 class="rekap-group__title" id="{{ $groupHeadingId }}">{{ $group['title'] }}</h2>
            <span class="badge badge--{{ $group['badge'] }}">
              <span class="mono">{{ $group['count'] }}</span>
              <span>transaksi</span>
            </span>
          </div>

          <div class="rekap-group__right">
            <span class="rekap-group__subtotal-label">Subtotal</span>
            <span class="rekap-group__subtotal">{{ FormatHelper::rupiah($group['subtotal']) }}</span>
          </div>
        </summary>

        <div class="rekap-group__body">
          @include('rekap.partials.table', ['rows' => $group['items'], 'headingId' => $groupHeadingId])
        </div>
      </details>
    @endforeach
  </div>
@endif

<!-- Dialog Konfirmasi Hapus Transaksi Native <dialog> -->
<dialog id="confirm-delete-tx-dialog" class="confirm-dialog" aria-labelledby="delete-tx-title" closedby="any">
  <h1 id="delete-tx-title" class="confirm-dialog__title">Hapus rekap pesanan?</h1>
  <p class="confirm-dialog__body">
    Anda akan menghapus transaksi <strong id="delete-tx-label">pesanan ini</strong> secara permanen dari rekap transaksi dan perhitungan pendapatan.
  </p>
  <form id="delete-tx-form" method="POST" action="#" class="confirm-dialog__actions">
    @csrf
    @method('DELETE')
    <button type="submit" formmethod="dialog" formnovalidate value="cancel" class="btn btn--quiet" autofocus>Batal</button>
    <button type="submit" value="delete" class="btn btn--danger">Ya, hapus pesanan</button>
  </form>
</dialog>
@endsection
