@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Ringkasan operasional, antrean cucian aktif, dan tarif layanan ASAS Laundry.')

@section('content')
@php use App\Support\FormatHelper; @endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Dashboard operasional</h1>
    <p class="page-header__desc">
      Pantau cucian yang sedang diproses, selesaikan order yang siap diambil, dan cek pendapatan secara langsung dari database.
    </p>
  </div>
  <div class="page-header__actions">
    <a class="btn btn--primary" href="{{ route('transaksi.index') }}">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      <span>Catat transaksi baru</span>
    </a>
    <a class="btn btn--quiet" href="{{ route('rekap.index') }}">Lihat rekap laporan</a>
  </div>
</section>

<!-- Strip statistik tabular dipisah hairline -->
<section class="stat-strip" aria-label="Ringkasan transaksi">
  <div class="stat-item">
    <span class="stat-item__label">Total transaksi</span>
    <span class="stat-item__value">{{ $ringkasan['total_transaksi'] }}</span>
    <span class="stat-item__note">Seluruh order tercatat</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Sedang diproses</span>
    <span class="stat-item__value">{{ $ringkasan['total_proses'] }}</span>
    <span class="stat-item__note">Menunggu penyelesaian</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Transaksi selesai</span>
    <span class="stat-item__value">{{ $ringkasan['total_selesai'] }}</span>
    <span class="stat-item__note">Siap atau sudah diambil</span>
  </div>
  <div class="stat-item">
    <span class="stat-item__label">Total pendapatan</span>
    <span class="stat-item__value stat-item__value--accent">{{ FormatHelper::rupiah($ringkasan['total_pendapatan']) }}</span>
    <span class="stat-item__note">Akumulasi nominal transaksi</span>
  </div>
</section>

<div class="workspace-grid">
  <!-- Panel kiri: Referensi tarif layanan -->
  <section class="panel" aria-labelledby="tarif-heading">
    <div class="panel__head">
      <h2 class="panel__title" id="tarif-heading">Tarif layanan aktif</h2>
      @if (auth()->user()->isAdmin())
        <a class="btn btn--quiet" href="{{ route('layanan.index') }}">Kelola</a>
      @endif
    </div>

    @if ($daftarLayanan->isEmpty())
      <div class="empty-state">
        <p class="empty-state__title">Belum ada data layanan</p>
        <p class="empty-state__desc">Tambahkan jenis layanan cuci agar kasir dapat mencatat transaksi.</p>
        @if (auth()->user()->isAdmin())
          <a class="btn btn--quiet" href="{{ route('layanan.index') }}">Tambah layanan</a>
        @endif
      </div>
    @else
      <ul class="tariff-list">
        @foreach ($daftarLayanan as $layanan)
          <li class="tariff-item">
            <div class="tariff-item__info">
              <span class="tariff-item__name">{{ $layanan->nama_layanan }}</span>
              <span class="tariff-item__est">Estimasi {{ $layanan->estimasi_selesai }}</span>
            </div>
            <span class="tariff-item__price">{{ FormatHelper::rupiah($layanan->harga_per_kg) }}/kg</span>
          </li>
        @endforeach
      </ul>
    @endif
  </section>

  <!-- Panel kanan: Antrean cucian aktif -->
  <section class="panel" aria-labelledby="antrean-heading">
    <div class="panel__head">
      <div>
        <h2 class="panel__title" id="antrean-heading">Antrean cucian dalam proses</h2>
        <p class="panel__meta">Klik tombol Tandai selesai saat cucian telah rampung dikerjakan.</p>
      </div>
      <span class="badge badge--proses">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        <span>{{ $antreanAktif->count() }} antrean</span>
      </span>
    </div>

    @if ($antreanAktif->isEmpty())
      <div class="empty-state">
        <p class="empty-state__title">Semua cucian sudah selesai</p>
        <p class="empty-state__desc">Tidak ada antrean dengan status Proses saat ini. Catat transaksi baru ketika pelanggan datang.</p>
        <a class="btn btn--quiet" href="{{ route('transaksi.index') }}">Buat transaksi baru</a>
      </div>
    @else
      <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="antrean-heading">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">Kode &amp; pelanggan</th>
              <th scope="col">Layanan</th>
              <th scope="col">Berat</th>
              <th scope="col">Total</th>
              <th scope="col">Status</th>
              <th scope="col" class="col-actions">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($antreanAktif as $row)
              <tr>
                <th scope="row">
                  <span class="cell-primary">{{ $row->nama_pelanggan }}</span>
                  <span class="cell-sub mono">{{ $row->kode_transaksi }} | {{ $row->no_hp }}</span>
                </th>
                <td data-label="Layanan">
                  <span class="cell-primary">{{ $row->layanan->nama_layanan }}</span>
                  <span class="cell-sub">{{ $row->layanan->estimasi_selesai }}</span>
                </td>
                <td data-label="Berat" class="col-num">{{ FormatHelper::berat($row->berat_kg) }}</td>
                <td data-label="Total" class="col-num">{{ FormatHelper::rupiah($row->total_harga) }}</td>
                <td data-label="Status">
                  <span class="badge badge--proses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                      <circle cx="12" cy="12" r="10"/>
                      <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <span>Proses</span>
                  </span>
                </td>
                <td data-label="Aksi" class="col-actions">
                  <form class="inline-form" method="POST" action="{{ route('transaksi.selesai', $row) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn--quiet">Tandai selesai</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
</div>
@endsection
