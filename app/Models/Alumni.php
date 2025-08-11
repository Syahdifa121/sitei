<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Alumni extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'alumni';

    protected $fillable = [
        'user_nim',
        'alamat',
        'no_telepon',
        'deskripsi_diri',
        'keahlian',
        'instagram',
        'facebook',
        'linkedin',
        'twitter',
        'kode_pt',
        'fakultas',
        'kode_prodi',
        'tahun_lulus',
        'nomor_induk_kependudukan',
        'nomor_pokok_wajib_pajak',
        'status_saat_ini',
        'bekerja_6_bulan_setelah_lulus',
        'bulan_mendapat_pekerjaan',
        'pendapatan_per_bulan',
        'lokasi_pekerjaan_provinsi',
        'lokasi_pekerjaan_kabupaten',
        'jenis_perusahaan',
        'jenis_perusahaan_lainnya',
        'nama_perusahaan',
        'posisi_wirausaha',
        'tingkat_tempat_kerja',
        'studi_lanjut_sumber_biaya',
        'studi_lanjut_kode_pt',
        'studi_lanjut_program_studi',
        'studi_lanjut_tanggal_masuk',
        'sumber_pembiayaan_kuliah',
        'sumber_pembiayaan_kuliah_lainnya',
        'hubungan_studi_pekerjaan',
        'pendidikan_sesuai_pekerjaan',
        'kompetensi_etika_lulus',
        'kompetensi_etika_saat_ini',
        'kompetensi_keahlian_bidang_lulus',
        'kompetensi_keahlian_bidang_saat_ini',
        'kompetensi_bahasa_inggris_lulus',
        'kompetensi_bahasa_inggris_saat_ini',
        'kompetensi_ti_lulus',
        'kompetensi_ti_saat_ini',
        'kompetensi_komunikasi_lulus',
        'kompetensi_komunikasi_saat_ini',
        'kompetensi_kerjasama_lulus',
        'kompetensi_kerjasama_saat_ini',
        'kompetensi_pengembangan_diri_lulus',
        'kompetensi_pengembangan_diri_saat_ini',
        'penekanan_perkuliahan',
        'penekanan_demontrasi',
        'penekanan_proyek_riset',
        'penekanan_magang',
        'penekanan_praktikum',
        'penekanan_kerja_lapangan',
        'penekanan_diskusi',
        'waktu_mulai_mencari_kerja',
        'bulan_sebelum_lulus_mencari_kerja',
        'bulan_setelah_lulus_mencari_kerja',
        'cari_kerja_iklan_koran',
        'cari_kerja_tanpa_lowongan',
        'cari_kerja_pameran',
        'cari_kerja_internet',
        'cari_kerja_dihubungi_perusahaan',
        'cari_kerja_kemenakertrans',
        'cari_kerja_agen_tenaga_kerja',
        'cari_kerja_pusat_karir',
        'cari_kerja_kemahasiswaan_alumni',
        'cari_kerja_jejaring',
        'cari_kerja_relasi',
        'cari_kerja_bisnis_sendiri',
        'cari_kerja_magang',
        'cari_kerja_tempat_kerja_sama',
        'cari_kerja_lainnya_pilih',
        'cari_kerja_lainnya_isi',
        'jumlah_instansi_dilamar',
        'jumlah_instansi_merespons',
        'jumlah_instansi_wawancara',
        'situasi_saat_ini',
        'situasi_saat_ini_lainnya',
        'aktif_mencari_pekerjaan_4_minggu',
        'aktif_mencari_pekerjaan_lainnya',
        'alasan_pekerjaan_sesuai_saat_ini',
        'alasan_mudah_dapat_kerja',
        'alasan_prospek_baik',
        'alasan_bidang_berbeda_tapi_sesuai',
        'alasan_promosi_posisi',
        'alasan_penghasilan_lebih_tinggi',
        'alasan_pekerjaan_aman',
        'alasan_pekerjaan_menarik',
        'alasan_pekerjaan_fleksibel',
        'alasan_dekat_dengan_rumah',
        'alasan_kebutuhan_keluarga',
        'alasan_karir_lain',
        'alasan_lainnya_pilih',
        'alasan_lainnya_isi',
        'beasiswa_masa_kuliah',
        'beasiswa_lainnya',
        'org_bem_universitas',
        'org_bem_fakultas',
        'org_dpm_universitas',
        'org_dpm_fakultas',
        'org_ukm_universitas',
        'org_lso_fakultas',
        'org_hmj',
        'org_hmprodi',
        'org_hmi',
        'org_gmki',
        'org_pmkri',
        'org_pmii',
        'org_kammi',
        'org_cimsa',
        'org_lainnya_pilih',
        'org_lainnya_isi',
        'saran_untuk_universitas',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alumni) {
            $alumni->id = Str::uuid();
        });
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'user_nim', 'nim');
    }
}