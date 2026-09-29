<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $kode_aatk
 * @property string $nama_barang
 * @property string $kategori
 * @property int $stok
 * @property string $satuan
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RiwayatStok> $riwayatStok
 * @property-read int|null $riwayat_stok_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereKodeAatk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereStok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BarangAatk whereUpdatedAt($value)
 */
	class BarangAatk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $judul
 * @property string $pesan
 * @property string $tipe
 * @property string|null $url
 * @property bool $dibaca
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereDibaca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi wherePesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereUserId($value)
 */
	class Notifikasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $nomor_permintaan
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string|null $catatan
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PermintaanItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereNomorPermintaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permintaan whereUserId($value)
 */
	class Permintaan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $permintaan_id
 * @property string $nama_barang
 * @property int $jumlah
 * @property int|null $jumlah_disetujui
 * @property string|null $satuan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Permintaan $permintaan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereJumlahDisetujui($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereNamaBarang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem wherePermintaanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermintaanItem whereUpdatedAt($value)
 */
	class PermintaanItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $barang_aatk_id
 * @property string $jenis
 * @property int $jumlah
 * @property string|null $keterangan
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BarangAatk $barang
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereBarangAatkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereJumlah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RiwayatStok whereUserId($value)
 */
	class RiwayatStok extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $nip
 * @property string|null $username
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string|null $jabatan
 * @property string|null $unit_kerja
 * @property string|null $ruangan
 * @property string|null $telepon
 * @property string|null $foto
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notifikasi> $notifikasi
 * @property-read int|null $notifikasi_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permintaan> $permintaans
 * @property-read int|null $permintaans_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNip($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelepon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUnitKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

