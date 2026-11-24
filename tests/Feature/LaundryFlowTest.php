<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\Transaksi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian End-to-End Alur Aplikasi ASAS Laundry (Laravel Feature Test)
 */
class LaundryFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/layanan')->assertRedirect('/login');
        $this->get('/transaksi')->assertRedirect('/login');
        $this->get('/rekap')->assertRedirect('/login');
    }

    public function test_invalid_login_shows_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'salah123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_admin_can_login_and_manage_layanan_with_fk_protection(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->assertSame('Faqih Rachman', $admin->nama_lengkap);

        // Login sebagai Admin
        $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ])->assertRedirect('/dashboard');

        // Dashboard menampilkan 14 transaksi & total Rp 636.000
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Faqih Rachman')
            ->assertSee('Data Layanan')
            ->assertSee('636.000');

        // Validasi harga negatif ditolak
        $this->from('/layanan')
            ->post('/layanan', [
                'nama_layanan'     => 'Tes Negatif',
                'harga_per_kg'     => -5000,
                'estimasi_selesai' => '1 hari',
            ])
            ->assertRedirect('/layanan')
            ->assertSessionHasErrors('harga_per_kg');

        // Tambah layanan baru
        $this->post('/layanan', [
            'nama_layanan'     => 'Cuci Selimut',
            'harga_per_kg'     => 15000,
            'estimasi_selesai' => '2 hari',
        ])->assertRedirect('/layanan');

        $layananBaru = Layanan::where('nama_layanan', 'Cuci Selimut')->firstOrFail();

        // Ubah layanan
        $this->put("/layanan/{$layananBaru->id}", [
            'nama_layanan'     => 'Cuci Selimut Tebal',
            'harga_per_kg'     => 18000,
            'estimasi_selesai' => '2-3 hari',
        ])->assertRedirect('/layanan');

        $this->assertDatabaseHas('layanan', [
            'id'           => $layananBaru->id,
            'nama_layanan' => 'Cuci Selimut Tebal',
            'harga_per_kg' => 18000,
        ]);

        // Proteksi FK: layanan yang masih dipakai transaksi (Reguler) tidak boleh terhapus
        $reguler = Layanan::where('nama_layanan', 'Reguler')->firstOrFail();
        $this->delete("/layanan/{$reguler->id}")
            ->assertRedirect('/layanan')
            ->assertSessionHas('flash_type', 'error');
        $this->assertDatabaseHas('layanan', ['id' => $reguler->id]);

        // Layanan yang belum dipakai transaksi berhasil dihapus
        $this->delete("/layanan/{$layananBaru->id}")
            ->assertRedirect('/layanan')
            ->assertSessionHas('flash_type', 'success');
        $this->assertDatabaseMissing('layanan', ['id' => $layananBaru->id]);
    }

    public function test_kasir_is_blocked_from_layanan_and_can_run_transaksi_rekap_struk_delete(): void
    {
        // Login sebagai Kasir
        $this->post('/login', [
            'username' => 'kasir',
            'password' => 'kasir123',
        ])->assertRedirect('/dashboard');

        // Kasir diblokir dari CRUD Data Layanan (GET, POST, DELETE)
        $this->get('/layanan')
            ->assertRedirect('/dashboard')
            ->assertSessionHas('flash_type', 'error');

        $this->post('/layanan', [
            'nama_layanan'     => 'Ilegal',
            'harga_per_kg'     => 5000,
            'estimasi_selesai' => '1 hari',
        ])->assertRedirect('/dashboard');
        $this->assertDatabaseMissing('layanan', ['nama_layanan' => 'Ilegal']);

        // Catat transaksi baru (Ekspres Rp 12.000/kg x 2.5 kg = Rp 30.000)
        $ekspres = Layanan::where('nama_layanan', 'Ekspres')->firstOrFail();
        $this->post('/transaksi', [
            'nama_pelanggan' => 'Surya Pratama',
            'no_hp'          => '081355667788',
            'layanan_id'     => $ekspres->id,
            'berat_kg'       => '2.5',
        ])->assertRedirect('/transaksi');

        $trx = Transaksi::where('nama_pelanggan', 'Surya Pratama')->firstOrFail();
        $this->assertSame(30000, $trx->total_harga);
        $this->assertSame('Proses', $trx->status);

        // Rekap SQL JOIN & filter URL menampilkan transaksi baru dan total Rp 666.000
        $this->get('/rekap')
            ->assertOk()
            ->assertSee('Surya Pratama')
            ->assertSee('666.000');

        $this->get('/rekap?status=Proses&q=Surya')
            ->assertOk()
            ->assertSee('Surya Pratama')
            ->assertDontSee('Budi Santoso');

        // Mode pengelompokan Per Layanan, Per Tanggal, dan Per Halaman (5 baris)
        $this->get('/rekap?group=layanan')
            ->assertOk()
            ->assertSee('Layanan Ekspres')
            ->assertSee('Layanan Reguler');

        $this->get('/rekap?group=tanggal')
            ->assertOk()
            ->assertSee('Subtotal');

        $this->get('/rekap?group=halaman&page=2')
            ->assertOk()
            ->assertSee('halaman 2 dari 3');

        // Halaman cetak struk menampilkan detail lengkap
        $this->get("/rekap/{$trx->id}/struk")
            ->assertOk()
            ->assertSee($trx->kode_transaksi)
            ->assertSee('Surya Pratama')
            ->assertSee('30.000');

        // Update status dari Proses menjadi Selesai
        $this->from('/rekap')
            ->patch("/transaksi/{$trx->id}/selesai")
            ->assertRedirect('/rekap');

        $trx->refresh();
        $this->assertSame('Selesai', $trx->status);
        $this->assertNotNull($trx->tanggal_selesai);

        // Hapus transaksi terpilih dari rekap pesanan
        $this->from('/rekap')
            ->delete("/transaksi/{$trx->id}")
            ->assertRedirect('/rekap')
            ->assertSessionHas('flash_type', 'success');

        $this->assertDatabaseMissing('transaksi', ['id' => $trx->id]);

        // Logout
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }
}
