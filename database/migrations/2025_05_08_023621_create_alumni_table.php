<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_nim');
            $table->foreign('user_nim')->references('nim')->on('mahasiswas')->onDelete('cascade');

            // biodata
            $table->string('nama');
            $table->string('email');
            $table->string('alamat')->nullable();
            $table->string('no_telepon');
            $table->string('deskripsi_diri')->nullable();
            $table->string('keahlian')->nullable();

            // sosial media
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();

            // data form alumni
            $table->string('kode_pt');
            $table->string('fakultas')->nullable();
            $table->string('kode_prodi');
            $table->string('tahun_lulus');
            $table->string('nomor_induk_kependudukan');
            $table->string('nomor_pokok_wajib_pajak')->nullable();

            // --------------------->
            // KUISIONER WAJIB
            // --------------------->

            $table->tinyInteger('status_saat_ini')->nullable(); // f8

            $table->boolean('bekerja_6_bulan_setelah_lulus')->nullable(); // f5-04
            $table->integer('bulan_mendapat_pekerjaan')->nullable(); // f5-02
            $table->integer('pendapatan_per_bulan')->nullable(); // f5-05

            $table->string('lokasi_pekerjaan_provinsi')->nullable(); // f5-a1
            $table->string('lokasi_pekerjaan_kabupaten')->nullable(); // f5-a2

            $table->unsignedTinyInteger('jenis_perusahaan')->nullable(); // f11-01
            $table->string('jenis_perusahaan_lainnya')->nullable(); // f11-02 (isian jika pilih "lainnya")

            $table->string('nama_perusahaan')->nullable(); // f5b
            $table->string('pekerjaan_saat_ini')->nullable();
            $table->string('posisi_wirausaha')->nullable(); // f5c
            $table->string('tingkat_tempat_kerja')->nullable(); // f5d

            // Studi lanjut
            $table->string('studi_lanjut_sumber_biaya')->nullable(); // f18a
            $table->string('studi_lanjut_kode_pt')->nullable(); // f18b
            $table->string('studi_lanjut_program_studi')->nullable(); // f18c
            $table->date('studi_lanjut_tanggal_masuk')->nullable(); // f18d

            // Sumber pembiayaan kuliah
            $table->unsignedTinyInteger('sumber_pembiayaan_kuliah')->nullable(); // f12-01
            $table->string('sumber_pembiayaan_kuliah_lainnya')->nullable();      // f12-02

            // Kesesuaian bidang studi dan pekerjaan
            $table->unsignedTinyInteger('hubungan_studi_pekerjaan')->nullable(); // f14

            // f15 - Tingkat pendidikan yang paling sesuai dengan pekerjaan saat ini
            $table->unsignedTinyInteger('pendidikan_sesuai_pekerjaan')->nullable(); // f15

            // f17 - Tingkat kompetensi saat lulus (A) dan saat ini (B)
            $table->tinyInteger('kompetensi_etika_lulus')->nullable(); // f1761
            $table->tinyInteger('kompetensi_etika_saat_ini')->nullable(); // f1762

            $table->tinyInteger('kompetensi_keahlian_bidang_lulus')->nullable(); // f1763
            $table->tinyInteger('kompetensi_keahlian_bidang_saat_ini')->nullable(); // f1764

            $table->tinyInteger('kompetensi_bahasa_inggris_lulus')->nullable(); // f1765
            $table->tinyInteger('kompetensi_bahasa_inggris_saat_ini')->nullable(); // f1766

            $table->tinyInteger('kompetensi_ti_lulus')->nullable(); // f1767
            $table->tinyInteger('kompetensi_ti_saat_ini')->nullable(); // f1768

            $table->tinyInteger('kompetensi_komunikasi_lulus')->nullable(); // f1769
            $table->tinyInteger('kompetensi_komunikasi_saat_ini')->nullable(); // f1770

            $table->tinyInteger('kompetensi_kerjasama_lulus')->nullable(); // f1771
            $table->tinyInteger('kompetensi_kerjasama_saat_ini')->nullable(); // f1772

            $table->tinyInteger('kompetensi_pengembangan_diri_lulus')->nullable(); // f1773
            $table->tinyInteger('kompetensi_pengembangan_diri_saat_ini')->nullable(); // f1774

            // --------------------->
            // KUISIONER LAINNYA
            // --------------------->

            // f2 - Penekanan metode pembelajaran (opsional)
            $table->tinyInteger('penekanan_perkuliahan')->nullable(); // f21
            $table->tinyInteger('penekanan_demontrasi')->nullable(); // f22
            $table->tinyInteger('penekanan_proyek_riset')->nullable(); // f23
            $table->tinyInteger('penekanan_magang')->nullable(); // f24
            $table->tinyInteger('penekanan_praktikum')->nullable(); // f25
            $table->tinyInteger('penekanan_kerja_lapangan')->nullable(); // f26
            $table->tinyInteger('penekanan_diskusi')->nullable(); // f27

            // f301 - Kapan mulai mencari pekerjaan
            $table->tinyInteger('waktu_mulai_mencari_kerja')->nullable(); // f301
            $table->integer('bulan_sebelum_lulus_mencari_kerja')->nullable(); // f302 (jika pilih sebelum lulus)
            $table->integer('bulan_setelah_lulus_mencari_kerja')->nullable(); // f303 (jika pilih sesudah lulus)
            
            // f4 - Cara mencari pekerjaan (masing-masing opsi sebagai kolom boolean)
            $table->boolean('cari_kerja_iklan_koran')->default(0); // f401
            $table->boolean('cari_kerja_tanpa_lowongan')->default(0); // f402
            $table->boolean('cari_kerja_pameran')->default(0); // f403
            $table->boolean('cari_kerja_internet')->default(0); // f404
            $table->boolean('cari_kerja_dihubungi_perusahaan')->default(0); // f405
            $table->boolean('cari_kerja_kemenakertrans')->default(0); // f406
            $table->boolean('cari_kerja_agen_tenaga_kerja')->default(0); // f407
            $table->boolean('cari_kerja_pusat_karir')->default(0); // f408
            $table->boolean('cari_kerja_kemahasiswaan_alumni')->default(0); // f409
            $table->boolean('cari_kerja_jejaring')->default(0); // f410
            $table->boolean('cari_kerja_relasi')->default(0); // f411
            $table->boolean('cari_kerja_bisnis_sendiri')->default(0); // f412
            $table->boolean('cari_kerja_magang')->default(0); // f413
            $table->boolean('cari_kerja_tempat_kerja_sama')->default(0); // f414
            $table->boolean('cari_kerja_lainnya_pilih')->default(0); // f415
            $table->string('cari_kerja_lainnya_isi')->nullable();

            $table->integer('jumlah_instansi_dilamar')->nullable(); // f6
            $table->integer('jumlah_instansi_merespons')->nullable(); // f7
            $table->integer('jumlah_instansi_wawancara')->nullable(); // f7a

            // f901- f905 - Situasi saat ini
            $table->tinyInteger('situasi_saat_ini')->nullable();
            $table->string('situasi_saat_ini_lainnya')->nullable(); // f906

            // f10 - Aktif mencari pekerjaan selama 4 minggu terakhir
            $table->tinyInteger('aktif_mencari_pekerjaan_4_minggu')->nullable(); // f1001
            $table->string('aktif_mencari_pekerjaan_lainnya')->nullable(); // f1002

            // f16 - Alasan mencari pekerjaan
            $table->boolean('alasan_pekerjaan_sesuai_saat_ini')->default(0); // f16-01
            $table->boolean('alasan_mudah_dapat_kerja')->default(0); // f16-02
            $table->boolean('alasan_prospek_baik')->default(0); // f16-03
            $table->boolean('alasan_bidang_berbeda_tapi_sesuai')->default(0); // f16-04
            $table->boolean('alasan_promosi_posisi')->default(0); // f16-05
            $table->boolean('alasan_penghasilan_lebih_tinggi')->default(0); // f16-06
            $table->boolean('alasan_pekerjaan_aman')->default(0); // f16-07
            $table->boolean('alasan_pekerjaan_menarik')->default(0); // f16-08
            $table->boolean('alasan_pekerjaan_fleksibel')->default(0); // f16-09
            $table->boolean('alasan_dekat_dengan_rumah')->default(0); // f16-10
            $table->boolean('alasan_kebutuhan_keluarga')->default(0); // f16-11
            $table->boolean('alasan_karir_lain')->default(0); // f16-12
            $table->boolean('alasan_lainnya_pilih')->default(0); // f16-13
            $table->string('alasan_lainnya_isi')->nullable(); // f16-14

            // f19 - Beasiswa
            $table->tinyInteger('beasiswa_masa_kuliah')->nullable(); // f1901-1922
            $table->string('beasiswa_lainnya')->nullable(); 

            // f20 - Organisasi
            $table->boolean('org_bem_universitas')->default(0);       // f2001
            $table->boolean('org_bem_fakultas')->default(0);          // f2002
            $table->boolean('org_dpm_universitas')->default(0);       // f2003
            $table->boolean('org_dpm_fakultas')->default(0);          // f2004
            $table->boolean('org_ukm_universitas')->default(0);       // f2005
            $table->boolean('org_lso_fakultas')->default(0);          // f2006
            $table->boolean('org_hmj')->default(0);                   // f2007
            $table->boolean('org_hmprodi')->default(0);               // f2008
            $table->boolean('org_hmi')->default(0);                   // f2009
            $table->boolean('org_gmki')->default(0);                  // f2010
            $table->boolean('org_pmkri')->default(0);                 // f2011
            $table->boolean('org_pmii')->default(0);                  // f2012
            $table->boolean('org_kammi')->default(0);                 // f2013
            $table->boolean('org_cimsa')->default(0);                 // f2014
            $table->boolean('org_lainnya_pilih')->default(0);         // f2015
            $table->string('org_lainnya_isi')->nullable();                   // f2016

            $table->text('saran_untuk_universitas')->nullable(); // f31

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
