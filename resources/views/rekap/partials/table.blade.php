@php use App\Support\FormatHelper; @endphp

<div class="table-wrapper" tabindex="0" role="region" aria-labelledby="{{ $headingId }}">
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
      @foreach ($rows as $row)
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
