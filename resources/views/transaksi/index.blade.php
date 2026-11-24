@extends('layouts.app')

@section('title', 'Transaksi Laundry')
@section('description', 'Catat order cucian baru dengan kalkulasi tarif otomatis dalam Rupiah dan perbarui status pengerjaan.')

@section('content')
@php use App\Support\FormatHelper; @endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Transaksi laundry</h1>
    <p class="page-header__desc">
      Catat cucian masuk dengan perhitungan tarif otomatis berdasarkan jenis layanan dan berat, lalu tandai selesai saat cucian rampung.
    </p>
  </div>
  <div class="page-header__actions">
    <a class="btn btn--quiet" href="{{ route('rekap.index') }}">Buka rekap transaksi</a>
  </div>
</section>

<div class="workspace-grid">
  <!-- Panel kiri: Form Input Transaksi Baru + Kalkulator Otomatis -->
  <section class="panel" aria-labelledby="form-transaksi-heading">
    <div class="panel__head">
      <h2 class="panel__title" id="form-transaksi-heading">Catat transaksi baru</h2>
      <span class="badge badge--proses">Status awal: Proses</span>
    </div>

    @if ($daftarLayanan->isEmpty())
      <div class="empty-state">
        <p class="empty-state__title">Belum ada data layanan</p>
        <p class="empty-state__desc">Transaksi belum dapat dibuat karena daftar jenis layanan masih kosong. Hubungi Admin untuk menambahkan layanan.</p>
      </div>
    @else
      @if ($errors->any())
        <div class="alert alert--error" role="alert">
          <span>Mohon periksa kembali data transaksi yang belum sesuai di bawah ini.</span>
        </div>
      @endif

      <form class="form-stack" method="POST" action="{{ route('transaksi.store') }}" aria-labelledby="form-transaksi-heading">
        @csrf

        <div class="field">
          <label class="field__label" for="nama_pelanggan">
            Nama pelanggan <span class="field__req">(wajib)</span>
          </label>
          <input
            class="input"
            type="text"
            id="nama_pelanggan"
            name="nama_pelanggan"
            value="{{ old('nama_pelanggan') }}"
            autocomplete="name"
            maxlength="100"
            placeholder="Contoh: Budi Santoso"
            data-error="nama-pelanggan-error"
            @error('nama_pelanggan') aria-invalid="true" @enderror
            required
          >
          <span class="field__error @error('nama_pelanggan') is-visible @enderror" id="nama-pelanggan-error">
            {{ $errors->first('nama_pelanggan') ?: 'Nama pelanggan wajib diisi.' }}
          </span>
        </div>

        <div class="field">
          <label class="field__label" for="no_hp">
            Nomor HP pelanggan <span class="field__req">(wajib)</span>
          </label>
          <input
            class="input mono"
            type="tel"
            id="no_hp"
            name="no_hp"
            value="{{ old('no_hp') }}"
            inputmode="tel"
            autocomplete="tel"
            maxlength="20"
            placeholder="081234567890"
            data-error="no-hp-error"
            @error('no_hp') aria-invalid="true" @enderror
            required
          >
          <span class="field__error @error('no_hp') is-visible @enderror" id="no-hp-error">
            {{ $errors->first('no_hp') ?: 'Masukkan nomor HP pelanggan yang valid.' }}
          </span>
        </div>

        <div class="field">
          <label class="field__label" for="layanan_id">
            Jenis layanan <span class="field__req">(wajib)</span>
          </label>
          <select
            class="select"
            id="layanan_id"
            name="layanan_id"
            data-error="layanan-id-error"
            @error('layanan_id') aria-invalid="true" @enderror
            required
          >
            <option value="">Pilih jenis layanan cuci</option>
            @foreach ($daftarLayanan as $layanan)
              <option
                value="{{ $layanan->id }}"
                data-harga="{{ $layanan->harga_per_kg }}"
                data-estimasi="{{ $layanan->estimasi_selesai }}"
                @selected((string) old('layanan_id') === (string) $layanan->id)
              >
                {{ $layanan->nama_layanan }} ({{ FormatHelper::rupiah($layanan->harga_per_kg) }}/kg, {{ $layanan->estimasi_selesai }})
              </option>
            @endforeach
          </select>
          <span class="field__error @error('layanan_id') is-visible @enderror" id="layanan-id-error">
            {{ $errors->first('layanan_id') ?: 'Pilih jenis layanan terlebih dahulu.' }}
          </span>
        </div>

        <div class="field">
          <label class="field__label" for="berat_kg">
            Berat cucian (kg) <span class="field__req">(wajib)</span>
          </label>
          <span class="field__hint" id="berat-hint">Gunakan desimal jika perlu, misalnya 2.5 untuk 2,5 kg.</span>
          <input
            class="input mono"
            type="number"
            id="berat_kg"
            name="berat_kg"
            value="{{ old('berat_kg') }}"
            min="0.1"
            max="500"
            step="0.1"
            inputmode="decimal"
            placeholder="3.5"
            aria-describedby="berat-hint"
            data-error="berat-kg-error"
            @error('berat_kg') aria-invalid="true" @enderror
            required
          >
          <span class="field__error @error('berat_kg') is-visible @enderror" id="berat-kg-error">
            {{ $errors->first('berat_kg') ?: 'Berat cucian harus lebih dari 0 kg.' }}
          </span>
        </div>

        <!-- Ringkasan Kalkulasi Otomatis -->
        <div class="calc-box" aria-live="polite" aria-atomic="true">
          <div class="calc-box__row">
            <span>Tarif layanan</span>
            <span class="mono" id="calc-rate">-</span>
          </div>
          <div class="calc-box__row">
            <span>Estimasi selesai</span>
            <span id="calc-estimasi">-</span>
          </div>
          <div class="calc-box__total">
            <span>Total biaya (berat x tarif)</span>
            <span class="calc-box__amount" id="calc-total">Rp&nbsp;0</span>
          </div>
        </div>

        <button type="submit" class="btn btn--primary btn--lg btn--full">Simpan transaksi</button>
      </form>
    @endif
  </section>

  <!-- Panel kanan: Daftar Transaksi Aktif (Proses) -->
  <section class="panel" aria-labelledby="proses-heading">
    <div class="panel__head">
      <div>
        <h2 class="panel__title" id="proses-heading">Cucian sedang diproses</h2>
        <p class="panel__meta">Total {{ $transaksiProses->count() }} transaksi menunggu perubahan status menjadi Selesai.</p>
      </div>
    </div>

    @if ($transaksiProses->isEmpty())
      <div class="empty-state">
        <p class="empty-state__title">Tidak ada cucian dalam antrean</p>
        <p class="empty-state__desc">Gunakan formulir di samping untuk mencatat transaksi pelanggan baru, atau lihat riwayat di Rekap Transaksi.</p>
        <a class="btn btn--quiet" href="{{ route('rekap.index') }}">Lihat semua transaksi</a>
      </div>
    @else
      <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="proses-heading">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">Kode &amp; pelanggan</th>
              <th scope="col">Layanan</th>
              <th scope="col">Berat</th>
              <th scope="col">Total biaya</th>
              <th scope="col">Status</th>
              <th scope="col" class="col-actions">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($transaksiProses as $row)
              <tr>
                <th scope="row">
                  <span class="cell-primary">{{ $row->nama_pelanggan }}</span>
                  <span class="cell-sub mono">{{ $row->kode_transaksi }} | {{ $row->no_hp }}</span>
                </th>
                <td data-label="Layanan">
                  <span class="cell-primary">{{ $row->layanan->nama_layanan }}</span>
                  <span class="cell-sub">{{ FormatHelper::rupiah($row->harga_saat_transaksi) }}/kg</span>
                </td>
                <td data-label="Berat" class="col-num">{{ FormatHelper::berat($row->berat_kg) }}</td>
                <td data-label="Total biaya" class="col-num">{{ FormatHelper::rupiah($row->total_harga) }}</td>
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
