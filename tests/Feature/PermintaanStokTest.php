<?php

namespace Tests\Feature;

use App\Models\BarangAatk;
use App\Models\Permintaan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermintaanStokTest extends TestCase
{
    use RefreshDatabase;

    public function test_stok_berkurang_saat_permintaan_diajukan(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $barang = BarangAatk::create([
            'kode_aatk' => 'ATK-001',
            'nama_barang' => 'Pulpen Hitam',
            'kategori' => 'Alat Tulis Kantor',
            'stok' => 20,
            'satuan' => 'Pcs',
        ]);

        $response = $this->actingAs($user)->post(route('permintaan.store'), [
            'aksi' => 'ajukan',
            'barang' => [
                ['nama' => 'Pulpen Hitam', 'jumlah' => 3, 'satuan' => 'Pcs'],
            ],
            'catatan' => 'Test pengajuan',
        ]);

        $response->assertRedirect();
        $this->assertEquals(17, $barang->fresh()->stok);
    }

    public function test_stok_dikembalikan_saat_permintaan_ditolak(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $barang = BarangAatk::create([
            'kode_aatk' => 'ATK-002',
            'nama_barang' => 'Kertas HVS A4',
            'kategori' => 'Kertas & Buku',
            'stok' => 50,
            'satuan' => 'Rim',
        ]);

        // User mengajukan permintaan (stok 50 -> 40)
        $this->actingAs($user)->post(route('permintaan.store'), [
            'aksi' => 'ajukan',
            'barang' => [
                ['nama' => 'Kertas HVS A4', 'jumlah' => 10, 'satuan' => 'Rim'],
            ],
        ]);

        $this->assertEquals(40, $barang->fresh()->stok);

        $permintaan = Permintaan::first();

        // Admin menolak (stok 40 -> 50)
        $response = $this->actingAs($admin)->post(route('admin.permintaan.tolak', $permintaan->id), [
            'alasan_penolakan' => 'Stok fisik rusak',
        ]);

        $response->assertRedirect();
        $this->assertEquals(50, $barang->fresh()->stok);
    }

    public function test_perubahan_status_oleh_admin_tampil_di_role_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $permintaan = Permintaan::create([
            'user_id' => $user->id,
            'nomor_permintaan' => 'REQ-2026-09-999',
            'tanggal' => now(),
            'status' => 'Menunggu',
        ]);

        // Admin setujui
        $this->actingAs($admin)->post(route('admin.permintaan.setujui', $permintaan->id));
        $this->assertEquals('Disetujui', $permintaan->fresh()->status);

        // Admin update ke Diproses
        $this->actingAs($admin)->post(route('admin.permintaan.update-status', $permintaan->id), ['status' => 'Diproses']);
        $this->assertEquals('Diproses', $permintaan->fresh()->status);

        // Admin update ke Dikirim
        $this->actingAs($admin)->post(route('admin.permintaan.update-status', $permintaan->id), ['status' => 'Dikirim']);
        $this->assertEquals('Dikirim', $permintaan->fresh()->status);

        // User melihat halaman detail permintaan
        $response = $this->actingAs($user)->get(route('permintaan.show', $permintaan->id));
        $response->assertStatus(200);
        $response->assertSee('Dikirim');
    }

    public function test_badge_notifikasi_menunggu_tampil_di_semua_halaman_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Buat 1 permintaan berstatus Menunggu
        Permintaan::create([
            'user_id' => $user->id,
            'nomor_permintaan' => 'REQ-2026-09-888',
            'tanggal' => now(),
            'status' => 'Menunggu',
        ]);

        // Cek halaman-halaman Admin
        $routes = [
            'admin.dashboard',
            'admin.permintaan.index',
            'admin.aatk.index',
            'admin.pegawai.index',
            'admin.laporan.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($admin)->get(route($routeName));
            $response->assertStatus(200);
            $response->assertSee('Kelola Permintaan');
            // Memastikan badge notifikasi jumlahMenunggu (angka 1) muncul pada menu Kelola Permintaan
            $response->assertSee('<span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">1</span>', false);
        }
    }
}
