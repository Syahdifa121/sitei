<!-- Step 2: Kuisioner Wajib -->
<div class="form-step active" id="kuisioner-wajib-step">
    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Kuisioner Wajib</h5>
        </div>
        <div class="card-body p-4">

            {{-- Pertanyaan 1: Status --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">Jelaskan status Anda saat ini? <span
                        class="text-danger">*</span></label>
                @foreach (['Bekerja (full time/part time)', 'Wiraswasta', 'Melanjutkan Pendidikan', 'Tidak Kerja tetapi sedang mencari kerja', 'Belum memungkinkan bekerja'] as $index => $option)
                    <div class="form-check">
                        <input class="form-check-input @error('status_saat_ini') is-invalid @enderror" type="radio" name="status_saat_ini"
                            id="status_saat_ini{{ $index + 1 }}" value="{{ $index + 1 }}"
                            onclick="handleStatusChange({{ $index + 1 }})"
                            {{ old('status_saat_ini', isset($alumni) ? $alumni->status_saat_ini : '') == $index + 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_saat_ini{{ $index + 1 }}">
                            [{{ $index + 1 }}] {{ $option }}
                        </label>
                    </div>
                @endforeach
                @error('status_saat_ini')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Pertanyaan lanjutan jika status == Bekerja (1) atau Wiraswasta (2) --}}
            <div id="kerja-details" style="display: none;">
                {{-- Pertanyaan 2 --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">
                        Apakah anda telah mendapatkan pekerjaan ≤ 6 bulan / termasuk bekerja sebelum lulus?
                        <span class="text-danger">*</span>
                    </label>

                    {{-- Pilihan Ya --}}
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bekerja_6_bulan_setelah_lulus" 
                            id="bekerja_6_bulan_setelah_lulus_1" value="1" 
                            onchange="toggleKerjaCepat()"
                            {{ old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? '') == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="bekerja_6_bulan_setelah_lulus_1">Ya</label>
                    </div>
                    <div class="ms-4 mt-2">
                        <label class="form-label">Dalam berapa bulan anda mendapatkan pekerjaan?</label>
                        <input type="number" class="form-control mb-2 @error('bulan_mendapat_pekerjaan_ya') is-invalid @enderror" 
                            name="bulan_mendapat_pekerjaan_ya"
                            id="bulan_mendapat_pekerjaan_ya"
                            value="{{ (old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 0) == 1) ? old('bulan_mendapat_pekerjaan_ya', $alumni->bulan_mendapat_pekerjaan ?? '') : '' }}"
                            {{ (old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 0) == 1) ? '' : 'disabled' }}>

                        <label class="form-label">Berapa rata-rata pendapatan anda per bulan? (take home pay)</label>
                        <input type="number" class="form-control @error('pendapatan_per_bulan') is-invalid @enderror" 
                            name="pendapatan_per_bulan" 
                            id="pendapatan_per_bulan"
                            value="{{ old('pendapatan_per_bulan', $alumni->pendapatan_per_bulan ?? '') }}"
                            {{ old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 0) == 1 ? '' : 'disabled' }}>
                        @error('bulan_mendapat_pekerjaan_ya')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                        @error('pendapatan_per_bulan')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Pilihan Tidak --}}
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="bekerja_6_bulan_setelah_lulus" 
                            id="bekerja_6_bulan_setelah_lulus_0" value="0" 
                            onchange="toggleKerjaCepat()"
                            {{ old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 1) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="bekerja_6_bulan_setelah_lulus_0">Tidak</label>
                    </div>
                    <div class="ms-4 mt-2">
                        <label class="form-label">Dalam berapa bulan anda mendapatkan pekerjaan?</label>
                        <input type="number" class="form-control @error('bulan_mendapat_pekerjaan_tidak') is-invalid @enderror" 
                            name="bulan_mendapat_pekerjaan_tidak"
                            id="bulan_mendapat_pekerjaan_tidak"
                            value="{{ old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 1) == 0 ? old('bulan_mendapat_pekerjaan_tidak', $alumni->bulan_mendapat_pekerjaan ?? '') : '' }}"
                            {{ old('bekerja_6_bulan_setelah_lulus', $alumni->bekerja_6_bulan_setelah_lulus ?? 1) == 0 ? '' : 'disabled' }}>
                        @error('bulan_mendapat_pekerjaan_tidak')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                    @error('bekerja_6_bulan_setelah_lulus')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pertanyaan 3 --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">Dimana lokasi tempat Anda bekerja? <span
                            class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="lokasi_pekerjaan_provinsi" class="form-label">Provinsi</label>
                            <select name="lokasi_pekerjaan_provinsi" id="lokasi_pekerjaan_provinsi" class="form-select @error('lokasi_pekerjaan_provinsi') is-invalid @enderror">
                                <option value="" selected disabled>Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province['name'] }}"
                                        {{ old('lokasi_pekerjaan_provinsi', $alumni->lokasi_pekerjaan_provinsi ?? '') == $province['name'] ? 'selected' : '' }}>
                                        {{ $province['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_pekerjaan_provinsi')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="lokasi_pekerjaan_kabupaten" class="form-label">Kab/Kota</label>
                            <select name="lokasi_pekerjaan_kabupaten" id="lokasi_pekerjaan_kabupaten" class="form-select @error('lokasi_pekerjaan_kabupaten') is-invalid @enderror">
                                <option value="" selected disabled>Pilih Kabupaten/Kota</option>
                                @if(isset($cities))
                                    @foreach ($cities as $city)
                                        <option value="{{ $city['name'] }}"
                                            {{ old('lokasi_pekerjaan_kabupaten', $alumni->lokasi_pekerjaan_kabupaten ?? '') == $city['name'] ? 'selected' : '' }}>
                                            {{ $city['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('lokasi_pekerjaan_kabupaten')
                                <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Pertanyaan 4 --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">
                        Apa jenis perusahaan/instansi/institusi tempat anda bekerja sekarang? <span class="text-danger">*</span>
                    </label>

                    @php
                        $jenis_perusahaan = [
                            1 => 'Instansi pemerintah',
                            2 => 'BUMN/BUMD',
                            3 => 'Institusi/Organisasi Multilateral',
                            4 => 'Organisasi non-profit/Lembaga Swadaya Masyarakat',
                            5 => 'Perusahaan swasta',
                            6 => 'Wiraswasta/perusahaan sendiri',
                            7 => 'Lainnya, tuliskan:',
                        ];
                    @endphp

                    @foreach ($jenis_perusahaan as $key => $label)
                        <div class="form-check">
                            <input class="form-check-input @error('jenis_perusahaan') is-invalid @enderror" 
                                type="radio"
                                name="jenis_perusahaan"
                                id="jenis_perusahaan_{{ $key }}"
                                value="{{ $key }}"
                                onchange="toggleJenisLainnya(this)"
                                {{ old('jenis_perusahaan', $alumni->jenis_perusahaan ?? '') == $key ? 'checked' : '' }}>
                            <label class="form-check-label" for="jenis_perusahaan_{{ $key }}">
                                [{{ $key }}] {{ $label }}
                            </label>

                            @if ($key === 7)
                            <input type="text"
                                class="form-control mt-2 @error('jenis_perusahaan_lainnya') is-invalid @enderror"
                                name="jenis_perusahaan_lainnya"
                                id="jenis_perusahaan_lainnya"
                                placeholder="Tuliskan jenis lainnya di sini..."
                                value="{{ old('jenis_perusahaan_lainnya', $alumni->jenis_perusahaan_lainnya ?? '') }}"
                                >
                                @if($errors->has('jenis_perusahaan_lainnya'))
                                    <span class="text-danger" style="font-size: 11px">{{ $errors->first('jenis_perusahaan_lainnya') }}</span>
                                @endif                            
                            @endif
                        </div>
                    @endforeach
                    @error('jenis_perusahaan')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pertanyaan 6 --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">
                        Apa nama perusahaan/kantor tempat anda bekerja?
                        <span class="text-danger">*</span>
                    </label>

                    <div>
                        <input type="text" 
                            class="form-control @error('nama_perusahaan') is-invalid @enderror" 
                            name="nama_perusahaan"
                            id="nama_perusahaan"
                            value="{{ old('nama_perusahaan', $alumni->nama_perusahaan ?? '') }}"
                            placeholder="Nama Perusahaan">
                        @error('nama_perusahaan')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Pertanyaan 6: Pekerjaan Saat Ini --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">
                        Apa pekerjaan anda saat ini?
                        <span class="text-danger">*</span>
                    </label>

                    <div>
                        <input type="text" 
                            class="form-control @error('pekerjaan_saat_ini') is-invalid @enderror" 
                            name="pekerjaan_saat_ini"
                            id="pekerjaan_saat_ini"
                            value="{{ old('pekerjaan_saat_ini', $alumni->pekerjaan_saat_ini ?? '') }}"
                            placeholder="Nama Pekerjaan Saat Ini">
                        @error('pekerjaan_saat_ini')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Pertanyaan 7: Tingkat Tempat Kerja --}}
                <div class="mb-4">
                    <label class="form-label fw-bold text-danger">
                        Apa tingkat tempat kerja anda?
                        <span class="text-danger">*</span>
                    </label>

                    <select class="form-select @error('tingkat_tempat_kerja') is-invalid @enderror" name="tingkat_tempat_kerja" id="tingkat_tempat_kerja">
                        <option value="" selected disabled>Pilih Tingkatan</option>
                        <option value="Lokal/wilayah/wiraswasta tidak berbadan hukum"
                            {{ old('tingkat_tempat_kerja', $alumni->tingkat_tempat_kerja ?? '') == 'Lokal/wilayah/wiraswasta tidak berbadan hukum' ? 'selected' : '' }}>
                            Lokal/wilayah/wiraswasta tidak berbadan hukum
                        </option>
                        <option value="Nasional/wiraswasta berbadan hukum"
                            {{ old('tingkat_tempat_kerja', $alumni->tingkat_tempat_kerja ?? '') == 'Nasional/wiraswasta berbadan hukum' ? 'selected' : '' }}>
                            Nasional/wiraswasta berbadan hukum
                        </option>
                        <option value="Multinasional/internasional"
                            {{ old('tingkat_tempat_kerja', $alumni->tingkat_tempat_kerja ?? '') == 'Multinasional/internasional' ? 'selected' : '' }}>
                            Multinasional/internasional
                        </option>
                    </select>
                    @error('tingkat_tempat_kerja')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pertanyaan 8: Khusus untuk Wiraswasta --}}
                <div class="mb-4" id="wiraswasta-details" style="display: none;">
                    <label class="form-label fw-bold text-danger">
                        Bila berwiraswasta, apa posisi/jabatan Anda saat ini?
                        <span class="text-danger">*</span>
                    </label>

                    @php
                        $posisiJabatanOptions = [
                            'Founder',
                            'Co-Founder',
                            'CEO/Direktur',
                            'Manager',
                            'Supervisor',
                            'Staff',
                            'Owner',
                        ];
                        $selectedPosisi = old('posisi_wirausaha', $alumni->posisi_wirausaha ?? '');
                    @endphp

                    <select class="form-select @error('posisi_wirausaha') is-invalid @enderror" 
                            name="posisi_wirausaha" 
                            id="posisi_wirausaha">
                        <option value="" selected disabled>Pilih Posisi</option>
                        @foreach ($posisiJabatanOptions as $posisi)
                            <option value="{{ $posisi }}"
                                {{ $selectedPosisi == $posisi ? 'selected' : '' }}>
                                {{ $posisi }}
                            </option>
                        @endforeach
                    </select>
                    @error('posisi_wirausaha')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Pertanyaan Studi Lanjut --}}
            <div class="mb-4">
                <label class="form-label fw-bold">
                    Pertanyaan studi lanjut
                </label>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sumber Biaya</label>
                        <select class="form-select @error('studi_lanjut_sumber_biaya') is-invalid @enderror" 
                                name="studi_lanjut_sumber_biaya" 
                                id="studi_lanjut_sumber_biaya">
                            <option value="" selected disabled>Pilih Sumber Biaya</option>
                            <option value="Beasiswa"
                                {{ old('studi_lanjut_sumber_biaya', $alumni->studi_lanjut_sumber_biaya ?? '') == 'Beasiswa' ? 'selected' : '' }}>
                                Beasiswa
                            </option>
                            <option value="Biaya Sendiri"
                                {{ old('studi_lanjut_sumber_biaya', $alumni->studi_lanjut_sumber_biaya ?? '') == 'Biaya Sendiri' ? 'selected' : '' }}>
                                Biaya Sendiri
                            </option>
                            <option value="Perusahaan/Kantor"
                                {{ old('studi_lanjut_sumber_biaya', $alumni->studi_lanjut_sumber_biaya ?? '') == 'Perusahaan/Kantor' ? 'selected' : '' }}>
                                Perusahaan/Kantor
                            </option>
                        </select>
                        @error('studi_lanjut_sumber_biaya')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" 
                            class="form-control @error('studi_lanjut_tanggal_masuk') is-invalid @enderror" 
                            name="studi_lanjut_tanggal_masuk" 
                            id="studi_lanjut_tanggal_masuk"
                            value="{{ old('studi_lanjut_tanggal_masuk', isset($alumni->studi_lanjut_tanggal_masuk) ? \Carbon\Carbon::parse($alumni->studi_lanjut_tanggal_masuk)->format('Y-m-d') : '') }}">
                        @error('studi_lanjut_tanggal_masuk')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perguruan Tinggi</label>
                        <select class="form-select @error('studi_lanjut_kode_pt') is-invalid @enderror" 
                                name="studi_lanjut_kode_pt" 
                                id="studi_lanjut_kode_pt">
                            <option value="" selected disabled>Pilih Kode PT</option>
                            <option value="001017"
                                {{ old('studi_lanjut_kode_pt', $alumni->studi_lanjut_kode_pt ?? '') == '001017' ? 'selected' : '' }}>
                                001017 - Universitas Riau
                            </option>
                            <!-- Tambahkan option lainnya sesuai kebutuhan -->
                        </select>
                        @error('studi_lanjut_kode_pt')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program Studi</label>
                        <select class="form-select @error('studi_lanjut_program_studi') is-invalid @enderror" 
                                name="studi_lanjut_program_studi" 
                                id="studi_lanjut_program_studi">
                            <option value="" selected disabled>Pilih Program Studi</option> <!-- Diperbaiki labelnya -->
                            <option value="55202"
                                {{ old('studi_lanjut_program_studi', $alumni->studi_lanjut_program_studi ?? '') == '55202' ? 'selected' : '' }}>
                                55202 - Teknik Informatika
                            </option>
                            <!-- Tambahkan option lainnya di sini -->
                        </select>
                        @error('studi_lanjut_program_studi')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Pertanyaan Sumberdana --}}
            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Sebutkan sumberdana dalam pembiayaan kuliah? <span class="text-danger">*</span>
                </label>
            
                @php
                    $sumberPembiayaanOptions = [
                        1 => 'Biaya Sendiri / Keluarga',
                        2 => 'Beasiswa ADIK',
                        3 => 'Beasiswa BIDIKMISI',
                        4 => 'Beasiswa PPA',
                        5 => 'Beasiswa AFIRMASI',
                        6 => 'Beasiswa Perusahaan/Swasta',
                        7 => 'Lainnya, tuliskan:'
                    ];
                    $selectedSumber = old('sumber_pembiayaan_kuliah', $alumni->sumber_pembiayaan_kuliah ?? null);
                    $showLainnya = $selectedSumber == 7;
                @endphp
            
                @foreach ($sumberPembiayaanOptions as $key => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('sumber_pembiayaan_kuliah') is-invalid @enderror" type="radio" name="sumber_pembiayaan_kuliah"
                            id="sumberdana_{{ $key }}" value="{{ $key }}"
                            onchange="toggleSumberdanaLainnya({{ $key }})"
                            {{ $selectedSumber == $key ? 'checked' : '' }}>
                        <label class="form-check-label" for="sumberdana_{{ $key }}">
                            [{{ $key }}] {{ $label }}
                        </label>
            
                        @if ($key == 7)
                            <input type="text" class="form-control mt-2 @error('sumber_pembiayaan_kuliah_lainnya') is-invalid @enderror" id="sumber_pembiayaan_kuliah_lainnya"
                                name="sumber_pembiayaan_kuliah_lainnya" 
                                value="{{ old('sumber_pembiayaan_kuliah_lainnya', $alumni->sumber_pembiayaan_kuliah_lainnya ?? '') }}"
                                placeholder="Tuliskan sumberdana lainnya di sini..."
                                {{ $showLainnya ? '' : 'disabled' }}>
                        @endif
                    </div>
                @endforeach
                
                @error('sumber_pembiayaan_kuliah')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
                @error('sumber_pembiayaan_kuliah_lainnya')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Seberapa erat hubungan antara bidang studi dengan pekerjaan anda? <span class="text-danger">*</span>
                </label>
            
                @php
                    $options = [
                        1 => 'Sangat Erat',
                        2 => 'Erat',
                        3 => 'Cukup Erat',
                        4 => 'Kurang Erat',
                        5 => 'Tidak Sama Sekali',
                    ];
                    $selectedValue = old('hubungan_studi_pekerjaan', $alumni->hubungan_studi_pekerjaan ?? null);
                @endphp
            
                @foreach ($options as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('hubungan_studi_pekerjaan') is-invalid @enderror" type="radio" name="hubungan_studi_pekerjaan"
                            id="kesesuaian_{{ $value }}" value="{{ $value }}"
                            {{ $selectedValue == $value ? 'checked' : '' }}>
                        <label class="form-check-label" for="kesesuaian_{{ $value }}">
                            [{{ $value }}] {{ $label }}
                        </label>
                    </div>
                @endforeach
                @error('hubungan_studi_pekerjaan')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-danger">
                    Tingkat pendidikan apa yang paling tepat/sesuai untuk pekerjaan anda saat ini?
                    <span class="text-danger">*</span>
                </label>
            
                @php
                    $options = [
                        1 => 'Setingkat Lebih Tinggi',
                        2 => 'Tingkat yang Sama',
                        3 => 'Setingkat Lebih Rendah',
                        4 => 'Tidak Perlu Pendidikan Tinggi',
                    ];
                    $selectedOption = old('pendidikan_sesuai_pekerjaan', $alumni->pendidikan_sesuai_pekerjaan ?? null);
                @endphp
            
                @foreach ($options as $index => $label)
                    <div class="form-check">
                        <input class="form-check-input @error('pendidikan_sesuai_pekerjaan') is-invalid @enderror" type="radio" name="pendidikan_sesuai_pekerjaan" 
                            id="tingkat_pendidikan_{{ $index }}" value="{{ $index }}"
                            {{ $selectedOption == $index ? 'checked' : '' }}>
                        <label class="form-check-label" for="tingkat_pendidikan_{{ $index }}">
                            [{{ $index }}] {{ $label }}
                        </label>
                    </div>
                @endforeach
                @error('pendidikan_sesuai_pekerjaan')
                    <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                @enderror
            </div>

            {{-- Pertanyaan Kompetensi --}}
            <div>
                <label class="form-label fw-bold text-danger">
                    Pada saat lulus, pada tingkat mana kompetensi di bawah ini anda kuasai? (A)<br>
                    Pada saat ini, pada tingkat mana kompetensi di bawah ini diperlukan dalam pekerjaan? (B) <span class="text-danger">*</span>
                </label>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="3" class="align-middle">Kompetensi</th>
                                <th colspan="5" class="text-center">A - Saat Lulus</th>
                                <th colspan="5" class="text-center">B - Saat Ini</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-center">
                                    <div class="d-flex justify-content-between">
                                        <div>Sangat rendah</div>
                                        <div>Sangat tinggi</div>
                                    </div>
                                </th>
                                <th colspan="5" class="text-center">
                                    <div class="d-flex justify-content-between">
                                        <div>Sangat rendah</div>
                                        <div>Sangat tinggi</div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                                <th class="text-center">1</th>
                                <th class="text-center">2</th>
                                <th class="text-center">3</th>
                                <th class="text-center">4</th>
                                <th class="text-center">5</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $competencies = [
                                    'etika' => ['label' => 'Etika', 'lulus' => $alumni->kompetensi_etika_lulus ?? null, 'saat_ini' => $alumni->kompetensi_etika_saat_ini ?? null],
                                    'keahlian_bidang' => ['label' => 'Keahlian berdasarkan bidang ilmu', 'lulus' => $alumni->kompetensi_keahlian_bidang_lulus ?? null, 'saat_ini' => $alumni->kompetensi_keahlian_bidang_saat_ini ?? null],
                                    'bahasa_inggris' => ['label' => 'Bahasa Inggris', 'lulus' => $alumni->kompetensi_bahasa_inggris_lulus ?? null, 'saat_ini' => $alumni->kompetensi_bahasa_inggris_saat_ini ?? null],
                                    'ti' => ['label' => 'Penggunaan Teknologi Informasi', 'lulus' => $alumni->kompetensi_ti_lulus ?? null, 'saat_ini' => $alumni->kompetensi_ti_saat_ini ?? null],
                                    'komunikasi' => ['label' => 'Komunikasi', 'lulus' => $alumni->kompetensi_komunikasi_lulus ?? null, 'saat_ini' => $alumni->kompetensi_komunikasi_saat_ini ?? null],
                                    'kerjasama' => ['label' => 'Kerja sama tim', 'lulus' => $alumni->kompetensi_kerjasama_lulus ?? null, 'saat_ini' => $alumni->kompetensi_kerjasama_saat_ini ?? null],
                                    'pengembangan_diri' => ['label' => 'Pengembangan Diri', 'lulus' => $alumni->kompetensi_pengembangan_diri_lulus ?? null, 'saat_ini' => $alumni->kompetensi_pengembangan_diri_saat_ini ?? null]
                                ];
                            @endphp
            
                            @foreach($competencies as $key => $competency)
                                <tr>
                                    <td>{{ $competency['label'] }}</td>
                                    <!-- Saat Lulus -->
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="text-center">
                                            <input class="form-check-input @error('kompetensi_{{ $key }}_lulus') is-invalid @enderror" type="radio" 
                                                name="kompetensi_{{ $key }}_lulus" 
                                                value="{{ $i }}"
                                                {{ old("kompetensi_{$key}_lulus", $competency['lulus']) == $i ? 'checked' : '' }}>
                                        </td>
                                    @endfor
                                    <!-- Saat Ini -->
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="text-center">
                                            <input class="form-check-input @error('kompetensi_{{ $key }}_saat_ini') is-invalid @enderror" type="radio" 
                                                name="kompetensi_{{ $key }}_saat_ini" 
                                                value="{{ $i }}"
                                                {{ old("kompetensi_{$key}_saat_ini", $competency['saat_ini']) == $i ? 'checked' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                                
                                @if($errors->has("kompetensi_{$key}_lulus") || $errors->has("kompetensi_{$key}_saat_ini"))
                                    <tr>
                                        <td colspan="11">
                                            @if($errors->has("kompetensi_{$key}_lulus"))
                                                <div class="text-danger" style="font-size: 11px">
                                                    {{ $errors->first("kompetensi_{$key}_lulus") }}
                                                </div>
                                            @endif
            
                                            @if($errors->has("kompetensi_{$key}_saat_ini"))
                                                <div class="text-danger" style="font-size: 11px">
                                                    {{ $errors->first("kompetensi_{$key}_saat_ini") }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const regencies = @json($regencies);
    const selectedKabupaten = '{{ old("lokasi_pekerjaan_kabupaten", $alumni->lokasi_pekerjaan_kabupaten ?? "") }}';

    document.getElementById('lokasi_pekerjaan_provinsi').addEventListener('change', function() {
        const selectedProvince = this.value;
        const kabupatenSelect = document.getElementById('lokasi_pekerjaan_kabupaten');

        kabupatenSelect.innerHTML = '<option value="" selected disabled>Pilih Kabupaten/Kota</option>';

        // Filter berdasarkan nama provinsi (bukan ID)
        const filtered = regencies.filter(item => {
            const province = @json($provinces).find(p => p.id === item.province_id);
            return province ? province.name === selectedProvince : false;
        });
        
        filtered.forEach(kab => {
            const opt = document.createElement('option');
            opt.value = kab.name; // Simpan nama kabupaten
            opt.textContent = kab.name;
            
            if (kab.name == selectedKabupaten) {
                opt.selected = true;
            }
            
            kabupatenSelect.appendChild(opt);
        });
    });

    // Trigger change event saat halaman load jika provinsi sudah dipilih
    document.addEventListener('DOMContentLoaded', function() {
        const selectedProvince = '{{ old("lokasi_pekerjaan_provinsi", $alumni->lokasi_pekerjaan_provinsi ?? "") }}';
        if (selectedProvince) {
            document.getElementById('lokasi_pekerjaan_provinsi').value = selectedProvince;
            document.getElementById('lokasi_pekerjaan_provinsi').dispatchEvent(new Event('change'));
        }
    });
</script>