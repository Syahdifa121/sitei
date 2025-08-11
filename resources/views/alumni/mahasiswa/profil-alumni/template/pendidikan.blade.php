@php
    use App\Models\Profil;

    $user = Auth::guard('mahasiswa')->user();
    $profil = Profil::where('user_nim', $user->nim)->first();
    $pendidikan = json_decode($profil->pendidikan ?? '[]', true);
    $pendidikanEmptyTemplate = view('alumni.mahasiswa.profil-alumni.template.pendidikan-empty')->render();
@endphp

<div class="pendidikan-list" id="pendidikan-list">
    @if($pendidikan)
        @foreach($pendidikan as $index => $item)
            <div class="form-section collapsed mb-3" id="pendidikan-{{ $index }}">
                <div class="section-header" onclick="toggleSection('pendidikan-{{ $index }}')">
                    <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                    <span class="section-title flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        Pendidikan {{ $index + 1 }} - {{ $item['nama_pendidikan'] }}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePendidikan({{ $index }})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="section-content" style="padding: 16px; display: none;">
                    <input type="hidden" name="pendidikan[{{ $index }}][urutan]" class="urutan-field" value="{{ $index }}">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Pendidikan</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pendidikan[{{ $index }}][nama_pendidikan]" 
                                   value="{{ $item['nama_pendidikan'] ?? '' }}" 
                                   placeholder="contoh: Universitas Riau">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Lokasi Pendidikan</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pendidikan[{{ $index }}][lokasi_pendidikan]" 
                                   value="{{ $item['lokasi_pendidikan'] ?? '' }}" 
                                   placeholder="contoh: Riau, Indonesia">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="pendidikan[{{ $index }}][tanggal_mulai]" 
                                   value="{{ $item['tanggal_mulai'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="pendidikan[{{ $index }}][tanggal_selesai]" 
                                   value="{{ $item['tanggal_selesai'] ?? '' }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tingkat Pendidikan</label>
                            <select class="form-control rounded-3" name="pendidikan[{{ $index }}][tingkat_pendidikan]">
                                <option value="">Pilih Tingkat</option>
                                <option value="SMA/SMK" @if(($item['tingkat_pendidikan'] ?? '') == 'SMA/SMK') selected @endif>SMA/SMK</option>
                                <option value="D3" @if(($item['tingkat_pendidikan'] ?? '') == 'D3') selected @endif>Diploma 3</option>
                                <option value="S1" @if(($item['tingkat_pendidikan'] ?? '') == 'S1') selected @endif>Sarjana (S1)</option>
                                <option value="S2" @if(($item['tingkat_pendidikan'] ?? '') == 'S2') selected @endif>Magister (S2)</option>
                                <option value="S3" @if(($item['tingkat_pendidikan'] ?? '') == 'S3') selected @endif>Doktor (S3)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Grade/IPK <span class="text-muted" style="font-size: 12px">(opsional)</span></label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pendidikan[{{ $index }}][grade]" 
                                   value="{{ $item['grade'] ?? '' }}" 
                                   placeholder="contoh: 3.75">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label class="form-label fw-bold">Aktivitas & Pencapaian</label>
                            <div class="list-input-container">
                                <ul class="list-group mb-2" id="pendidikan-{{ $index }}-aktivitas-list">
                                    @if(!empty($item['aktivitas_pencapaian']))
                                        @foreach($item['aktivitas_pencapaian'] as $aktivitasIndex => $aktivitas)
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                <span>{{ $aktivitas }}</span>
                                                <div>
                                                    <input type="hidden" 
                                                           name="pendidikan[{{ $index }}][aktivitas_pencapaian][]" 
                                                           value="{{ $aktivitas }}">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeListItem(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <input type="text" class="form-control rounded-3" 
                                       id="pendidikan-{{ $index }}-aktivitas-input" 
                                       placeholder="Masukkan aktivitas atau pencapaian, tekan Enter untuk menambah..." 
                                       onkeypress="handleListInput(event, 'pendidikan-{{ $index }}-aktivitas', 'pendidikan[{{ $index }}][aktivitas_pencapaian]')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Gunakan template kosong hanya jika tidak ada data -->
        {!! str_replace('{id}', 0, $pendidikanEmptyTemplate) !!}
        @php $pendidikanCount = 1; @endphp
    @endif
</div>