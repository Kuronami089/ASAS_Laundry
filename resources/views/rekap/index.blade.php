@extends('layouts.app')

@section('title', 'Rekap Transaksi Laundry')
@section('description', 'Laporan transaksi laundry relasional (SQL JOIN) lengkap dengan status Proses/Selesai, cetak struk, dan total pendapatan.')

@section('content')
@php use App\Support\FormatHelper; @endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Rekap transaksi laundry</h1>
    <p class="page-header__desc">
      Laporan keuangan dan riwayat order yang ditarik langsung melalui relasi tabel transaksi dan layanan.
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
          <a class="btn btn--quiet" href="{{ route('rekap.index') }}">Reset</a>
        @endif
      </div>
    </form>
  </search>
</section>

<!-- Tabel Rekap Transaksi Laundry -->
<section class="panel" aria-labelledby="tabel-rekap-heading">
  <div class="panel__head">
    <div>
      <h2 class="panel__title" id="tabel-rekap-heading">Daftar seluruh transaksi</h2>
      <p class="panel__meta">Menampilkan {{ $jumlahTransaksi }} baris hasil relasi transaksi dan layanan.</p>
    </div>
  </div>

  @if ($rekapTransaksi->isEmpty())
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
        <a class="btn btn--quiet" href="{{ route('rekap.index') }}">Reset filter</a>
      @else
        <a class="btn btn--quiet" href="{{ route('transaksi.index') }}">Catat transaksi baru</a>
      @endif
    </div>
  @else
    <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="tabel-rekap-heading">
      <table class="data-table">
        <thead>
          <tr>
            <th scope="col">Kode &amp; pelanggan</th>
            <th scope="col">Layanan &amp; tarif</th>
            <th scope="col">Berat</th>
            <th scope="col">Total biaya</th>
            <th scope="col">Status</th>
            <th scope="col">Waktu masuk</th>
            <th scope="col" class="col-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($rekapTransaksi as $row)
            <tr>
              <th scope="row">
                <span class="cell-primary">{{ $row->nama_pelanggan }}</span>
                <span class="cell-sub mono">{{ $row->kode_transaksi }} | {{ $row->no_hp }}</span>
              </th>
              <td data-label="Layanan &amp; tarif">
                <span class="cell-primary">{{ $row->nama_layanan }}</span>
                <span class="cell-sub mono">{{ FormatHelper::rupiah($row->harga_saat_transaksi) }}/kg</span>
              </td>
              <td data-label="Berat" class="col-num">{{ FormatHelper::berat($row->berat_kg) }}</td>
              <td data-label="Total biaya" class="col-num">{{ FormatHelper::rupiah($row->total_harga) }}</td>
              <td data-label="Status">
                @if ($row->status === 'Selesai')
                  <span class="badge badge--selesai">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <circle cx="12" cy="12" r="10"/>
                      <polyline points="9 12 11.5 14.5 16 9.5"/>
                    </svg>
                    <span>Selesai</span>
                  </span>
                @else
                  <span class="badge badge--proses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <circle cx="12" cy="12" r="10"/>
                      <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Proses</span>
                  </span>
                @endif
              </td>
              <td data-label="Waktu masuk">
                <span class="cell-primary mono">{{ FormatHelper::tanggal($row->tanggal_masuk) }}</span>
                @if (!empty($row->nama_petugas))
                  <span class="cell-sub">Petugas: {{ $row->nama_petugas }}</span>
                @endif
              </td>
              <td data-label="Aksi" class="col-actions">
                <div class="row-actions">
                  @if ($row->status === 'Proses')
                    <form class="inline-form" method="POST" action="{{ route('transaksi.selesai', $row->id) }}">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn--quiet">Selesai</button>
                    </form>
                  @endif

                  <a
                    class="btn btn--quiet btn--icon"
                    href="{{ route('rekap.struk', $row->id) }}"
                    target="_blank"
                    rel="noopener"
                    aria-label="Cetak struk {{ $row->kode_transaksi }}"
                    title="Cetak struk"
                  >
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/>
                      <path d="M8 7h8"/>
                      <path d="M8 11h8"/>
                      <path d="M8 15h5"/>
                    </svg>
                  </a>

                  <button
                    type="button"
                    class="btn btn--danger btn--icon"
                    data-delete-tx-action="{{ route('transaksi.destroy', $row->id) }}"
                    data-delete-tx-label="{{ $row->kode_transaksi }} ({{ $row->nama_pelanggan }})"
                    aria-label="Hapus transaksi {{ $row->kode_transaksi }}"
                    title="Hapus pesanan"
                  >
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                      <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                      <line x1="10" y1="11" x2="10" y2="17"/>
                      <line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</section>

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
