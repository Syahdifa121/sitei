<!-- Step 3: Kuisioner Lainnya -->
<div class="form-step active" id="kuisioner-lainnya-step">
    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Kuisioner Lainnya</h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Menurut anda seberapa besar penekanan pada metode pembelajaran di bawah ini dilaksanakan di
                    program studi anda? <span class="text-danger">*</span>
                </label>

                <div class="mb-4">
                    <label class="form-label fw-bold">Perkuliahan</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_perkuliahan') is-invalid @enderror" type="radio" 
                                   name="penekanan_perkuliahan" 
                                   id="penekanan_perkuliahan_{{ $value }}" 
                                   value="{{ $value }}"
                                   {{ old('penekanan_perkuliahan', $alumni->penekanan_perkuliahan ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_perkuliahan_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_perkuliahan')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Demonstrasi</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_demontrasi') is-invalid @enderror" type="radio" 
                                   name="penekanan_demontrasi" 
                                   id="penekanan_demontrasi_{{ $value }}" 
                                   value="{{ $value }}"
                                   {{ old('penekanan_demontrasi', $alumni->penekanan_demontrasi ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_demontrasi_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_demontrasi')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Partisipasi dalam proyek riset</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_proyek_riset') is-invalid @enderror" type="radio" 
                                   name="penekanan_proyek_riset" 
                                   id="penekanan_proyek_riset_{{ $value }}" 
                                   value="{{ $value }}"
                                   {{ old('penekanan_proyek_riset', $alumni->penekanan_proyek_riset ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_proyek_riset_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_proyek_riset')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Magang</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar', 
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_magang') is-invalid @enderror" type="radio" 
                                   name="penekanan_magang" 
                                   id="penekanan_magang_{{ $value }}" 
                                   value="{{ $value }}"
                                   {{ old('penekanan_magang', $alumni->penekanan_magang ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_magang_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_magang')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Praktikum</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_praktikum') is-invalid @enderror" type="radio"
                                   name="penekanan_praktikum"
                                   id="penekanan_praktikum_{{ $value }}"
                                   value="{{ $value }}"
                                   {{ old('penekanan_praktikum', $alumni->penekanan_praktikum ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_praktikum_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_praktikum')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Kerja Lapangan</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_kerja_lapangan') is-invalid @enderror" type="radio"
                                   name="penekanan_kerja_lapangan"
                                   id="penekanan_kerja_lapangan_{{ $value }}"
                                   value="{{ $value }}"
                                   {{ old('penekanan_kerja_lapangan', $alumni->penekanan_kerja_lapangan ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_kerja_lapangan_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_kerja_lapangan')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Diskusi</label>
                    
                    @foreach ([
                        1 => 'Sangat Besar',
                        2 => 'Besar',
                        3 => 'Cukup Besar',
                        4 => 'Kurang',
                        5 => 'Tidak Sama Sekali'
                    ] as $value => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('penekanan_diskusi') is-invalid @enderror" type="radio"
                                   name="penekanan_diskusi"
                                   id="penekanan_diskusi_{{ $value }}"
                                   value="{{ $value }}"
                                   {{ old('penekanan_diskusi', $alumni->penekanan_diskusi ?? null) == $value ? 'checked' : '' }}>
                            <label class="form-check-label" for="penekanan_diskusi_{{ $value }}">
                                [{{ $value }}] {{ $label }}
                            </label>
                        </div>
                    @endforeach
                    @error('penekanan_diskusi')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Pertanyaan 1: Kapan mulai mencari pekerjaan -->
            @php
            $waktuMencariKerjaOptions = [
                1 => [
                    'label' => "Kira-kira <input type=\"number\" class=\"form-control d-inline-block @error('bulan_sebelum_lulus') is-invalid @enderror\" style=\"width: 80px;\" name=\"bulan_sebelum_lulus\" value=\"".old('bulan_sebelum_lulus', $alumni->bulan_sebelum_lulus_mencari_kerja ?? null)."\" ". (old('waktu_mulai_mencari_kerja', $alumni->waktu_mulai_mencari_kerja ?? null) == 1 ? '' : 'disabled') ."> bulan sebelum lulus",
                    'value' => 1,
                    'has_input' => true
                ],
                2 => [
                    'label' => "Kira-kira <input type=\"number\" class=\"form-control d-inline-block @error('bulan_sesudah_lulus') is-invalid @enderror\" style=\"width: 80px;\" name=\"bulan_sesudah_lulus\" value=\"".old('bulan_sesudah_lulus', $alumni->bulan_setelah_lulus_mencari_kerja ?? null)."\" ". (old('waktu_mulai_mencari_kerja', $alumni->waktu_mulai_mencari_kerja ?? null) == 2 ? '' : 'disabled') ."> bulan sesudah lulus",
                    'value' => 2,
                    'has_input' => true
                ],
                3 => [
                    'label' => 'Saya tidak mencari kerja',
                    'value' => 3,
                    'has_input' => false
                ]
            ];
            @endphp

            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan <span class="text-danger">*</span>
                </label>
                
                @foreach($waktuMencariKerjaOptions as $key => $option)
                    <div class="form-check @if($option['has_input']) d-flex align-items-center @endif mt-2">
                        <input class="form-check-input @if($option['has_input']) me-2 @endif" 
                            type="radio"
                            name="waktu_mulai_mencari_kerja"
                            id="waktu_mulai_mencari_kerja_{{ $key }}"
                            value="{{ $option['value'] }}"
                            {{ old('waktu_mulai_mencari_kerja', $alumni->waktu_mulai_mencari_kerja ?? null) == $option['value'] ? 'checked' : '' }}
                            onchange="handleMulaiCariKerjaChange()">
                        <label class="form-check-label" for="waktu_mulai_mencari_kerja_{{ $key }}">
                            [{{ $key }}] {!! $option['label'] !!}
                        </label>
                    </div>
                @endforeach
                
                @error('waktu_mulai_mencari_kerja')
                    <div class="text-danger" style="font-size: 11px">{{ $message }}</div>
                @enderror
                @error('bulan_sebelum_lulus')
                    <div class="text-danger" style="font-size: 11px">{{ $message }}</div>
                @enderror
                @error('bulan_sesudah_lulus')
                    <div class="text-danger" style="font-size: 11px">{{ $message }}</div>
                @enderror
            </div>

            <!-- Pertanyaan 2: Bagaimana mencari pekerjaan -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu <span class="text-danger">*</span>
                </label>
            
                @php
                    // Define error message for minimum selection
                    $minSelectionError = $errors->first('cara_cari_kerja_min');
                @endphp
            
                @if($minSelectionError)
                    <div class="text-danger mb-2" style="font-size: 11px">{{ $minSelectionError }}</div>
                @endif
            
                @foreach ([
                    'cari_kerja_iklan_koran' => '[1] Melalui iklan di koran/majalah, brosur',
                    'cari_kerja_tanpa_lowongan' => '[2] Melamar ke perusahaan tanpa mengetahui lowongan yang ada',
                    'cari_kerja_pameran' => '[3] Pergi ke bursa/pameran kerja',
                    'cari_kerja_internet' => '[4] Mencari lewat internet/iklan online/milis',
                    'cari_kerja_dihubungi_perusahaan' => '[5] Dihubungi oleh perusahaan',
                    'cari_kerja_kemenakertrans' => '[6] Menghubungi Kemenakertrans',
                    'cari_kerja_agen_tenaga_kerja' => '[7] Menghubungi agen tenaga kerja komersial/swasta',
                    'cari_kerja_pusat_karir' => '[8] Memperoleh informasi dari pusat/kantor pengembangan karir fakultas/universitas',
                    'cari_kerja_kemahasiswaan_alumni' => '[9] Menghubungi kantor kemahasiswaan/hubungan alumni',
                    'cari_kerja_jejaring' => '[10] Membangun jejaring (network) sejak masih kuliah',
                    'cari_kerja_relasi' => '[11] Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.)',
                    'cari_kerja_bisnis_sendiri' => '[12] Membangun bisnis sendiri',
                    'cari_kerja_magang' => '[13] Melalui penempatan kerja atau magang',
                    'cari_kerja_tempat_kerja_sama' => '[14] Bekerja di tempat yang sama dengan tempat kerja semasa kuliah',
                    'cari_kerja_lainnya_pilih' => '[15] Lainnya:'
                ] as $value => $label)
                    <div class="form-check {{ $value === 'cari_kerja_lainnya_pilih' ? 'mb-2' : '' }}">
                        <input class="form-check-input @error($value) is-invalid @enderror" type="checkbox" 
                            name="{{ $value }}" 
                            id="cara_cari_kerja_{{ $loop->iteration }}" 
                            value="1" 
                            {{ old($value, $alumni->$value ?? null) ? 'checked' : '' }}
                            @if($value === 'cari_kerja_lainnya_pilih') 
                                onchange="document.getElementById('cara_cari_kerja_lainnya').disabled = !this.checked"
                            @endif>
                        <label class="form-check-label" for="cara_cari_kerja_{{ $loop->iteration }}">
                            {{ $label }}
                        </label>
                    </div>
                    
                    @if($value === 'cari_kerja_lainnya_pilih')
                        <div>
                            <input type="text" class="form-control mt-2 @error('cari_kerja_lainnya_isi') is-invalid @enderror" id="cara_cari_kerja_lainnya"
                                name="cari_kerja_lainnya_isi" 
                                value="{{ old('cari_kerja_lainnya_isi') }}"
                                placeholder="Sebutkan cara lainnya..." 
                                {{ old('cari_kerja_lainnya_pilih', $alumni->cari_kerja_lainnya_pilih ?? null) ? '' : 'disabled' }}>
                            @error('cari_kerja_lainnya_isi')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                    @error($value)
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                @endforeach
            </div>            

            <!-- Pertanyaan 3-5: Jumlah Perusahaan -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Berapa perusahaan/instansi/institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum
                    anda memperoleh pekerjaan pertama? <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" class="form-control @error('jumlah_instansi_dilamar') is-invalid @enderror" name="jumlah_instansi_dilamar"
                        min="0" placeholder="Jumlah perusahaan/instansi/institusi" value="{{ old('jumlah_instansi_dilamar', $alumni->jumlah_instansi_dilamar ?? null) }}">
                    <span class="input-group-text">perusahaan/instansi/institusi</span>
                </div>
                @error('jumlah_instansi_dilamar')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Berapa banyak perusahaan/instansi/institusi yang merespons lamaran anda? <span
                        class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" class="form-control @error('jumlah_instansi_merespons') is-invalid @enderror" name="jumlah_instansi_merespons"
                        min="0" placeholder="Jumlah perusahaan/instansi/institusi" value="{{ old('jumlah_instansi_merespons', $alumni->jumlah_instansi_merespons ?? null) }}">
                    <span class="input-group-text">perusahaan/instansi/institusi</span>
                </div>
                @error('jumlah_instansi_merespons')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Berapa banyak perusahaan/instansi/institusi yang mengundang anda untuk wawancara? <span
                        class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <input type="number" class="form-control @error('jumlah_instansi_wawancara') is-invalid @enderror" name="jumlah_instansi_wawancara"
                        min="0" placeholder="Jumlah perusahaan/instansi/institusi" value="{{ old('jumlah_instansi_wawancara', $alumni->jumlah_instansi_wawancara ?? null) }}">
                    <span class="input-group-text">perusahaan/instansi/institusi</span>
                </div>
                @error('jumlah_instansi_wawancara')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Pertanyaan situasi saat ini --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Bagaimana anda menggambarkan situasi anda saat ini? <span class="text-danger">*</span>
                </label>
            
                @php
                    $options = [
                        1 => 'Saya masih belajar/melanjutkan kuliah profesi',
                        2 => 'Saya Menikah',
                        3 => 'Saya sibuk dengan keluarga anak-anak',
                        4 => 'Saya sekarang sedang mencari pekerjaan',
                        5 => 'Lainnya, tuliskan:'
                    ];
                @endphp
            
                @foreach ($options as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('situasi_saat_ini') is-invalid @enderror" type="radio" 
                            name="situasi_saat_ini" 
                            id="situasi_saat_ini_{{ $value }}" 
                            value="{{ $value }}"
                            onclick="toggleSituasiSaatIniLainnya(this)"
                            {{ old('situasi_saat_ini', $alumni->situasi_saat_ini ?? null) == $value ? 'checked' : '' }}>
                        <label class="form-check-label" for="situasi_saat_ini_{{ $value }}">
                            [{{ $value }}] {{ $label }}
                        </label>
                        @if ($value === 5)
                            <input type="text" class="form-control mt-2 @error('situasi_saat_ini_lainnya') is-invalid @enderror" id="input_lainnya"
                                name="situasi_saat_ini_lainnya"
                                placeholder="Tuliskan situasi saat ini lainnya..."
                                value="{{ old('situasi_saat_ini_lainnya', $alumni->situasi_saat_ini_lainnya ?? null) }}"
                                {{ old('situasi_saat_ini', $alumni->situasi_saat_ini ?? null) == 5 ? '' : 'disabled' }}>
                            @error('situasi_saat_ini_lainnya')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                @endforeach
                @error('situasi_saat_ini')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>         

            <!-- Pertanyaan Pencarian Kerja Aktif -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? <span class="text-danger">*</span>
                </label>
            
                @php
                    $options = [
                        1 => 'Tidak',
                        2 => 'Tidak, tapi saya sedang menunggu hasil lamaran kerja',
                        3 => 'Ya, saya akan mulai bekerja dalam 2 minggu ke depan',
                        4 => 'Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan',
                        5 => 'Lainnya, tuliskan:'
                    ];
                @endphp
            
                @foreach ($options as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('aktif_mencari_pekerjaan_4_minggu') is-invalid @enderror" type="radio" 
                            name="aktif_mencari_pekerjaan_4_minggu" 
                            id="aktif_mencari_pekerjaan_{{ $value }}" 
                            value="{{ $value }}"
                            onclick="toggleAktifMencariLainnya(this)"
                            {{ old('aktif_mencari_pekerjaan_4_minggu', $alumni->aktif_mencari_pekerjaan_4_minggu ?? null) == $value ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif_mencari_pekerjaan_{{ $value }}">
                            [{{ $value }}] {{ $label }}
                        </label>
                        @if ($value === 5)
                            <input type="text" class="form-control mt-2 @error('aktif_mencari_pekerjaan_lainnya') is-invalid @enderror" 
                                id="input_aktif_mencari_pekerjaan_lainnya"
                                name="aktif_mencari_pekerjaan_lainnya"
                                value="{{ old('aktif_mencari_pekerjaan_lainnya', $alumni->aktif_mencari_pekerjaan_lainnya ?? null) }}"
                                placeholder="Sebutkan lainnya di sini..."
                                {{ old('aktif_mencari_pekerjaan_4_minggu', $alumni->aktif_mencari_pekerjaan_4_minggu ?? null) == 5 ? '' : 'disabled' }}>
                            @error('aktif_mencari_pekerjaan_lainnya')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                @endforeach
                @error('aktif_mencari_pekerjaan_4_minggu')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>
            
            <!-- Alasan mengambil pekerjaan tidak sesuai pendidikan -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu <span class="text-danger">*</span>
                </label>
            
                @php
                    $alasanOptions = [
                        'alasan_pekerjaan_sesuai_saat_ini' => '[1] Pertanyaan tidak sesuai; pekerjaan saya sekarang sudah sesuai dengan pendidikan saya.',
                        'alasan_mudah_dapat_kerja' => '[2] Saya belum mendapatkan pekerjaan yang lebih sesuai.',
                        'alasan_prospek_baik' => '[3] Di pekerjaan ini saya memperoleh prospek karir yang baik.',
                        'alasan_bidang_berbeda_tapi_sesuai' => '[4] Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya.',
                        'alasan_promosi_posisi' => '[5] Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya.',
                        'alasan_penghasilan_lebih_tinggi' => '[6] Saya dapat memperoleh pendapatan yang lebih tinggi di pekerjaan ini.',
                        'alasan_pekerjaan_aman' => '[7] Pekerjaan saya saat ini lebih aman/terjamin/secure.',
                        'alasan_pekerjaan_menarik' => '[8] Pekerjaan saya saat ini lebih menarik.',
                        'alasan_pekerjaan_fleksibel' => '[9] Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal yang fleksibel, dll.',
                        'alasan_dekat_dengan_rumah' => '[10] Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya.',
                        'alasan_kebutuhan_keluarga' => '[11] Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya.',
                        'alasan_karir_lain' => '[12] Pada awal menitit karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya.',
                        'alasan_lainnya_pilih' => '[13] Lainnya:'
                    ];
                @endphp
            
                @foreach($alasanOptions as $field => $label)
                    <div class="form-check">
                        <input class="form-check-input @error($field) is-invalid @enderror" type="checkbox" 
                            name="{{ $field }}" 
                            id="{{ $field }}" 
                            value="1"
                            {{ old($field, $alumni->$field ?? null) ? 'checked' : '' }}
                            @if($field === 'alasan_lainnya_pilih') 
                                onchange="document.getElementById('alasan_lainnya_isi').disabled = !this.checked"
                            @endif>
                        <label class="form-check-label" for="{{ $field }}">
                            {{ $label }}
                        </label>
                        @if($field === 'alasan_lainnya_pilih')
                            <div>
                                <input type="text" class="form-control mt-2 @error('alasan_lainnya_isi') is-invalid @enderror" id="alasan_lainnya_isi" 
                                    name="alasan_lainnya_isi" 
                                    value="{{ old('alasan_lainnya_isi', $alumni->alasan_lainnya_isi ?? null) }}"
                                    placeholder="Sebutkan alasan lainnya..." 
                                    {{ old('alasan_lainnya_pilih', $alumni->alasan_lainnya_pilih ?? null) ? '' : 'disabled' }}>
                                @error('alasan_lainnya_isi')
                                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    @error($field)
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                @endforeach
            </div>

            <!-- Pertanyaan Beasiswa yang diikuti -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Beasiswa apakah yang pernah anda dapatkan pada masa kuliah? <span class="text-danger">*</span>
                </label>
            
                @php
                    $beasiswaOptions = [
                        1 => 'Beasiswa ADIK',
                        2 => 'Beasiswa BIDIKMISI/KIP-K',
                        3 => 'Peningkatan Prestasi Akademik (PPA)',
                        4 => 'Beasiswa AFIRMASI DIKTI (Papua dan Wilayan 3T)',
                        5 => 'Beasiswa Bantuan Belajar Mahasiswa (BBM)',
                        6 => 'Beasiswa Peningkatan Prestasi Ekstrakurikuler',
                        7 => 'Beasiswa SLTA FKIP',
                        8 => 'Beasiswa BIDIKMISI PEMPROV',
                        9 => 'Beasiswa Bhakti Negeri',
                        10 => 'Beasiswa BANSOS',
                        11 => 'Beasiswa TANOTO',
                        12 => 'Beasiswa Bank Indonesia',
                        13 => 'Beasiswa Karya Salemba Empat',
                        14 => 'Beasiswa Yayasan Salim',
                        15 => 'Beasiswa PT. DJARUM',
                        16 => 'Beasiswa Lazis PLN',
                        17 => 'Beasiswa BAZNAS Pusat',
                        18 => 'Beasiswa BAZNAS Kampar',
                        19 => 'Beasiswa PT. Chevron',
                        20 => 'Beasiswa RAPP',
                        21 => 'Bukan Penerima Beasiswa',
                        22 => 'Lainnya, tuliskan:'
                    ];
                @endphp
            
                @foreach($beasiswaOptions as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('beasiswa_masa_kuliah') is-invalid @enderror" type="radio" 
                            name="beasiswa_masa_kuliah" 
                            id="beasiswa_masa_kuliah_{{ $value }}" 
                            value="{{ $value }}"
                            onclick="toggleBeasiswaLainnya(this)"
                            {{ old('beasiswa_masa_kuliah', $alumni->beasiswa_masa_kuliah ?? null) == $value ? 'checked' : '' }}>
                        <label class="form-check-label" for="beasiswa_masa_kuliah_{{ $value }}">
                            [{{ $value }}] {{ $label }}
                        </label>
                        @if($value == 22)
                            <input type="text" class="form-control mt-2 @error('beasiswa_lainnya') is-invalid @enderror" id="beasiswa_lainnya"
                                name="beasiswa_lainnya" 
                                value="{{ old('beasiswa_lainnya', $alumni->beasiswa_lainnya ?? null) }}"
                                placeholder="Sebutkan lainnya di sini..."
                                {{ old('beasiswa_masa_kuliah', $alumni->beasiswa_masa_kuliah ?? null) == 22 ? '' : 'disabled' }}>
                            @error('beasiswa_lainnya')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                @endforeach
                @error('beasiswa_masa_kuliah')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            <!-- Organisasi selama masa kuliah -->
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Organisasi internal/eksternal apakah yang pernah anda ikuti selama masa kuliah? Jawaban bisa lebih dari satu <span class="text-danger">*</span>
                </label>
            
                @php
                    $organisasiOptions = [
                        'org_bem_universitas' => '[1] BEM UNIVERSITAS',
                        'org_bem_fakultas' => '[2] BEM FAKULTAS',
                        'org_dpm_universitas' => '[3] DPM UNIVERSITAS',
                        'org_dpm_fakultas' => '[4] DPM FAKULTAS',
                        'org_ukm_universitas' => '[5] UKM UNIVERSITAS',
                        'org_lso_fakultas' => '[6] LSO FAKULTAS',
                        'org_hmj' => '[7] HIMPUNAN MAHASISWA JURUSAN',
                        'org_hmprodi' => '[8] HIMPUNAN MAHASISWA PRODI',
                        'org_hmi' => '[9] HMI',
                        'org_gmki' => '[10] GMKI',
                        'org_pmkri' => '[11] PMKRI',
                        'org_pmii' => '[12] PMII',
                        'org_kammi' => '[13] KAMMI',
                        'org_cimsa' => '[14] CIMSA',
                        'org_lainnya_pilih' => '[15] Lainnya:'
                    ];
                @endphp
            
                @foreach($organisasiOptions as $field => $label)
                    <div class="form-check">
                        <input class="form-check-input @error($field) is-invalid @enderror" type="checkbox" 
                            name="{{ $field }}" 
                            id="{{ $field }}" 
                            value="1"
                            {{ old($field, $alumni->$field ?? null) ? 'checked' : '' }}
                            @if($field === 'org_lainnya_pilih') 
                                onchange="
                                    document.getElementById('org_lainnya_isi').disabled = !this.checked;
                                    if(!this.checked) document.getElementById('org_lainnya_isi').value = '';
                                "
                            @endif>
                        <label class="form-check-label" for="{{ $field }}">
                            {{ $label }}
                        </label>
                        @if($field === 'org_lainnya_pilih')
                            <div>
                                <input type="text" class="form-control mt-2 @error('org_lainnya_isi') is-invalid @enderror" id="org_lainnya_isi" 
                                    name="org_lainnya_isi" 
                                    value="{{ old('org_lainnya_isi', $alumni->org_lainnya_isi ?? null) }}"
                                    placeholder="Sebutkan organisasi lainnya..." 
                                    {{ old('org_lainnya_pilih', $alumni->org_lainnya_pilih ?? null) ? '' : 'disabled' }}>
                                @error('org_lainnya_isi')
                                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    @error($field)
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                @endforeach
            </div>

            {{-- Saran dan Masukan --}}
            <div>
                <label class="form-label fw-bold text-danger">
                    Demi kemajuan Universitas Riau, silahkan berikan saran atau masukan anda <span class="text-danger">*</span>
                </label>
                <textarea class="form-control rounded-3 @error('saran_untuk_universitas') is-invalid @enderror" name="saran_untuk_universitas" rows="4"
                    placeholder="Tulis masukan dan saran Anda...">{{ old('saran_untuk_universitas', $alumni->saran_untuk_universitas ?? null) }}</textarea>
                @error('saran_untuk_universitas')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>