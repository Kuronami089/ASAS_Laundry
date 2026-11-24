@extends('layouts.app')

@section('title', 'Data Layanan')
@section('description', 'Kelola jenis layanan cuci, tarif per kilogram, dan estimasi waktu pengerjaan.')

@section('content')
@php
    use App\Support\FormatHelper;
    $isEditMode = $editLayanan !== null;
@endphp

<section class="page-header">
  <div class="page-header__title">
    <h1>Data layanan cuci</h1>
    <p class="page-header__desc">
      Tambah, ubah, atau hapus paket layanan. Layanan yang masih terhubung dengan transaksi dilindungi agar laporan tetap konsisten.
    </p>
  </div>
  <div class="page-header__actions">
    <a class="btn btn--quiet" href="{{ route('transaksi.index') }}">Ke form transaksi</a>
  </div>
</section>

<div class="workspace-grid">
  <!-- Panel kiri: Form Tambah / Ubah Layanan -->
  <section class="panel" aria-labelledby="form-layanan-heading">
    <div class="panel__head">
      <h2 class="panel__title" id="form-layanan-heading">
        {{ $isEditMode ? 'Ubah data layanan' : 'Tambah layanan baru' }}
      </h2>
      @if ($isEditMode)
        <a class="btn btn--quiet" href="{{ route('layanan.index') }}">Batal ubah</a>
      @endif
    </div>

    @if ($errors->any())
      <div class="alert alert--error" role="alert">
        <span>Periksa kembali input formulir yang ditandai di bawah ini.</span>
      </div>
    @endif

    <form
      class="form-stack"
      method="POST"
      action="{{ $isEditMode ? route('layanan.update', $editLayanan) : route('layanan.store') }}"
      aria-labelledby="form-layanan-heading"
    >
      @csrf
      @if ($isEditMode)
        @method('PUT')
      @endif

      <div class="field">
        <label class="field__label" for="nama_layanan">
          Nama layanan <span class="field__req">(wajib)</span>
        </label>
        <input
          class="input"
          type="text"
          id="nama_layanan"
          name="nama_layanan"
          value="{{ old('nama_layanan', $editLayanan?->nama_layanan) }}"
          maxlength="100"
          placeholder="Contoh: Reguler, Ekspres, Superkilat"
          data-error="nama-layanan-error"
          @error('nama_layanan') aria-invalid="true" @enderror
          required
        >
        <span class="field__error @error('nama_layanan') is-visible @enderror" id="nama-layanan-error">
          {{ $errors->first('nama_layanan') ?: 'Nama layanan wajib diisi.' }}
        </span>
      </div>

      <div class="field">
        <label class="field__label" for="harga_per_kg">
          Harga per kg (Rp) <span class="field__req">(wajib)</span>
        </label>
        <span class="field__hint" id="harga-hint">Masukkan angka bulat lebih dari 0, tanpa titik.</span>
        <input
          class="input mono"
          type="number"
          id="harga_per_kg"
          name="harga_per_kg"
          value="{{ old('harga_per_kg', $editLayanan?->harga_per_kg) }}"
          min="1"
          step="500"
          inputmode="numeric"
          placeholder="7000"
          aria-describedby="harga-hint"
          data-error="harga-error"
          @error('harga_per_kg') aria-invalid="true" @enderror
          required
        >
        <span class="field__error @error('harga_per_kg') is-visible @enderror" id="harga-error">
          {{ $errors->first('harga_per_kg') ?: 'Masukkan tarif per kg berupa angka lebih dari 0.' }}
        </span>
      </div>

      <div class="field">
        <label class="field__label" for="estimasi_selesai">
          Estimasi selesai <span class="field__req">(wajib)</span>
        </label>
        <input
          class="input"
          type="text"
          id="estimasi_selesai"
          name="estimasi_selesai"
          value="{{ old('estimasi_selesai', $editLayanan?->estimasi_selesai) }}"
          maxlength="60"
          placeholder="Contoh: 3-4 hari atau 5 jam"
          data-error="estimasi-error"
          @error('estimasi_selesai') aria-invalid="true" @enderror
          required
        >
        <span class="field__error @error('estimasi_selesai') is-visible @enderror" id="estimasi-error">
          {{ $errors->first('estimasi_selesai') ?: 'Estimasi selesai wajib diisi.' }}
        </span>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn--primary">
          {{ $isEditMode ? 'Simpan perubahan' : 'Simpan layanan' }}
        </button>
        @if ($isEditMode)
          <a class="btn btn--quiet" href="{{ route('layanan.index') }}">Batal</a>
        @endif
      </div>
    </form>
  </section>

  <!-- Panel kanan: Tabel Daftar Layanan -->
  <section class="panel" aria-labelledby="daftar-layanan-heading">
    <div class="panel__head">
      <div>
        <h2 class="panel__title" id="daftar-layanan-heading">Daftar paket layanan</h2>
        <p class="panel__meta">Total {{ $daftarLayanan->count() }} jenis layanan tersimpan di database.</p>
      </div>
    </div>

    @if ($daftarLayanan->isEmpty())
      <div class="empty-state">
        <p class="empty-state__title">Belum ada jenis layanan</p>
        <p class="empty-state__desc">Tambahkan layanan Reguler, Ekspres, atau Superkilat melalui formulir di samping.</p>
      </div>
    @else
      <div class="table-wrapper" tabindex="0" role="region" aria-labelledby="daftar-layanan-heading">
        <table class="data-table">
          <thead>
            <tr>
              <th scope="col">Nama layanan</th>
              <th scope="col">Tarif per kg</th>
              <th scope="col">Estimasi selesai</th>
              <th scope="col">Relasi transaksi</th>
              <th scope="col" class="col-actions">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($daftarLayanan as $item)
              <tr>
                <th scope="row">
                  <span class="cell-primary">{{ $item->nama_layanan }}</span>
                </th>
                <td data-label="Tarif per kg" class="col-num">{{ FormatHelper::rupiah($item->harga_per_kg) }}</td>
                <td data-label="Estimasi selesai">{{ $item->estimasi_selesai }}</td>
                <td data-label="Relasi transaksi">
                  <span class="badge badge--neutral">
                    <span class="mono">{{ $item->transaksi_count }}</span>
                    <span>transaksi</span>
                  </span>
                </td>
                <td data-label="Aksi" class="col-actions">
                  <div class="row-actions">
                    <a class="btn btn--quiet" href="{{ route('layanan.index', ['edit' => $item->id]) }}">Ubah</a>
                    <button
                      type="button"
                      class="btn btn--danger"
                      data-delete-service-action="{{ route('layanan.destroy', $item) }}"
                      data-delete-service-name="{{ $item->nama_layanan }}"
                    >
                      Hapus
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
</div>

<!-- Dialog Konfirmasi Hapus Layanan Native <dialog> -->
<dialog id="confirm-delete-dialog" class="confirm-dialog" aria-labelledby="delete-dialog-title" closedby="any">
  <h1 id="delete-dialog-title" class="confirm-dialog__title">Hapus data layanan?</h1>
  <p class="confirm-dialog__body">
    Anda akan menghapus layanan <strong id="delete-service-name">layanan ini</strong>. Jika layanan masih digunakan oleh transaksi, penghapusan akan ditolak otomatis agar laporan tetap utuh.
  </p>
  <form id="delete-service-form" method="POST" action="#" class="confirm-dialog__actions">
    @csrf
    @method('DELETE')
    <button type="submit" formmethod="dialog" formnovalidate value="cancel" class="btn btn--quiet" autofocus>Batal</button>
    <button type="submit" value="delete" class="btn btn--danger">Ya, hapus layanan</button>
  </form>
</dialog>
@endsection
