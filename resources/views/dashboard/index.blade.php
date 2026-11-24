@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Ringkasan operasional, distribusi pendapatan layanan, dan antrean cucian aktif ASAS Laundry.')

@section('content')
@php use App\Support\FormatHelper; @endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Dashboard operasional</h1>
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

<!-- Bar Statistik Animasi (terinspirasi StatsSection, angka count-up easeOutExpo) -->
<section class="stats-showcase" aria-label="Ringkasan statistik operasional" data-stats-showcase>
  <div class="stats-showcase__cell">
    <span class="stats-showcase__value" data-count-to="{{ $ringkasan['total_transaksi'] }}" data-count-format="int">{{ $ringkasan['total_transaksi'] }}</span>
    <span class="stats-showcase__label">Total transaksi</span>
  </div>
  <div class="stats-showcase__cell">
    <span class="stats-showcase__value" data-count-to="{{ $ringkasan['total_proses'] }}" data-count-format="int">{{ $ringkasan['total_proses'] }}</span>
    <span class="stats-showcase__label">Sedang diproses</span>
  </div>
  <div class="stats-showcase__cell">
    <span class="stats-showcase__value" data-count-to="{{ $ringkasan['total_selesai'] }}" data-count-format="int">{{ $ringkasan['total_selesai'] }}</span>
    <span class="stats-showcase__label">Transaksi selesai</span>
  </div>
  <div class="stats-showcase__cell">
    <span class="stats-showcase__value stats-showcase__value--accent" data-count-to="{{ $ringkasan['total_pendapatan'] }}" data-count-format="rupiah">{{ FormatHelper::rupiah($ringkasan['total_pendapatan']) }}</span>
    <span class="stats-showcase__label">Total pendapatan</span>
  </div>
</section>

<div class="workspace-grid">
  <!-- Kolom kiri: Grafik Donut Bergradasi + Tarif Layanan -->
  <div class="dashboard-side">
    <section class="panel" aria-labelledby="donut-heading">
      <div class="panel__head">
        <h2 class="panel__title" id="donut-heading">Pendapatan per layanan</h2>
      </div>

      @if ($ringkasan['total_pendapatan'] <= 0)
        <div class="empty-state">
          <p class="empty-state__title">Belum ada pendapatan tercatat</p>
        </div>
      @else
        <div class="donut-wrap" data-insight-donut data-donut-items='@json($distribusiLayanan)'>
          <div class="donut-stage">
            <svg class="donut-svg" viewBox="0 0 260 260" role="img" aria-label="Grafik lingkaran distribusi pendapatan per jenis layanan">
              <defs>
                <linearGradient id="dash-grad-0" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="var(--color-chart-1-a)"/>
                  <stop offset="100%" stop-color="var(--color-chart-1-b)"/>
                </linearGradient>
                <linearGradient id="dash-grad-1" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="var(--color-chart-2-a)"/>
                  <stop offset="100%" stop-color="var(--color-chart-2-b)"/>
                </linearGradient>
                <linearGradient id="dash-grad-2" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="var(--color-chart-3-a)"/>
                  <stop offset="100%" stop-color="var(--color-chart-3-b)"/>
                </linearGradient>
                <linearGradient id="dash-grad-3" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="var(--color-chart-4-a)"/>
                  <stop offset="100%" stop-color="var(--color-chart-4-b)"/>
                </linearGradient>
              </defs>
              <circle class="donut-track" cx="130" cy="130" r="102"/>
              <g data-donut-arcs></g>
            </svg>

            <div class="donut-center" aria-live="polite" aria-atomic="true">
              <span class="donut-center__label" data-donut-label>Total pendapatan</span>
              <span class="donut-center__value" data-donut-value>{{ FormatHelper::rupiah($ringkasan['total_pendapatan']) }}</span>
              <span class="donut-center__share" data-donut-share>{{ $ringkasan['total_transaksi'] }} order</span>
            </div>
          </div>

          <ul class="donut-legend" data-donut-legend>
            @foreach ($distribusiLayanan as $idx => $seg)
              @php
                $pct = $ringkasan['total_pendapatan'] > 0
                    ? round(($seg['value'] / $ringkasan['total_pendapatan']) * 100, 1)
                    : 0;
              @endphp
              <li>
                <button type="button" class="donut-legend__btn" data-donut-index="{{ $idx }}">
                  <span class="donut-legend__left">
                    <span class="donut-legend__swatch donut-legend__swatch--{{ $idx % 4 }}" aria-hidden="true"></span>
                    <span>{{ $seg['name'] }}</span>
                  </span>
                  <span class="donut-legend__right">
                    <span class="donut-legend__pct">{{ number_format($pct, 1, ',', '.') }}%</span>
                    <span class="donut-legend__val">{{ FormatHelper::rupiah($seg['value']) }}</span>
                  </span>
                </button>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </section>

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
  </div>

  <!-- Kolom kanan: Antrean cucian aktif -->
  <section class="panel" aria-labelledby="antrean-heading">
    <div class="panel__head">
      <h2 class="panel__title" id="antrean-heading">Antrean cucian dalam proses</h2>
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
