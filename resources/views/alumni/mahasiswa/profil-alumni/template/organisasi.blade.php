@php
    use App\Models\Profil;

    $user = Auth::guard('mahasiswa')->user();
    $profil = Profil::where('user_nim', $user->nim)->first();
    $organisasi = json_decode($profil->organisasi ?? '[]', true);
    $organisasiEmptyTemplate = view('alumni.mahasiswa.profil-alumni.template.organisasi-empty')->render();
@endphp

<div class="organisasi-list" id="organisasi-list">
    @if($organisasi && count($organisasi) > 0)
        @foreach($organisasi as $index => $item)
            <div class="form-section collapsed mb-3" id="organisasi-{{ $index }}">
                <div class="section-header" onclick="toggleSection('organisasi-{{ $index }}')">
                    <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                    <span class="section-title flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        Organisasi {{ $index + 1 }} - {{ $item['nama_organisasi'] }}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOrganisasi({{ $index }})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="section-content" style="padding: 16px; display: none;">
                    <input type="hidden" name="organisasi[{{ $index }}][urutan]" class="urutan-field" value="{{ $index }}">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Organisasi</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="organisasi[{{ $index }}][nama_organisasi]" 
                                   value="{{ $item['nama_organisasi'] ?? '' }}" 
                                   placeholder="contoh: Himpunan Mahasiswa Teknik">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Posisi</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="organisasi[{{ $index }}][posisi]" 
                                   value="{{ $item['posisi'] ?? '' }}" 
                                   placeholder="contoh: Ketua Divisi IT">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Lokasi Organisasi</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="organisasi[{{ $index }}][lokasi_organisasi]" 
                                   value="{{ $item['lokasi_organisasi'] ?? '' }}" 
                                   placeholder="contoh: Jakarta, Indonesia">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Deskripsi Organisasi</label>
                            <textarea class="form-control rounded-3" 
                                      name="organisasi[{{ $index }}][deskripsi_organisasi]" 
                                      rows="2" 
                                      placeholder="Jelaskan singkat tentang organisasi...">{{ $item['deskripsi_organisasi'] ?? '' }}</textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="organisasi[{{ $index }}][tanggal_mulai]" 
                                   value="{{ $item['tanggal_mulai'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="organisasi[{{ $index }}][tanggal_selesai]" 
                                   value="{{ $item['tanggal_selesai'] ?? '' }}">
                            <div class="form-check mt-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="organisasi[{{ $index }}][masih_aktif]" 
                                       value="1"
                                       @if(!empty($item['masih_aktif']) && $item['masih_aktif'] == 1) checked @endif>
                                <label class="form-check-label">Masih aktif</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label class="form-label fw-bold">Deskripsi Pekerjaan</label>
                            <div class="list-input-container">
                                <ul class="list-group mb-2" id="organisasi-{{ $index }}-deskripsi-list">
                                    @if(!empty($item['deskripsi_pekerjaan']))
                                        @foreach($item['deskripsi_pekerjaan'] as $deskripsiIndex => $deskripsi)
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                <span>{{ $deskripsi }}</span>
                                                <div>
                                                    <input type="hidden" 
                                                           name="organisasi[{{ $index }}][deskripsi_pekerjaan][]" 
                                                           value="{{ $deskripsi }}">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeListItem(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <input type="text" class="form-control rounded-3" 
                                       id="organisasi-{{ $index }}-deskripsi-input" 
                                       placeholder="Masukkan deskripsi pekerjaan, tekan Enter untuk menambah..." 
                                       onkeypress="handleListInput(event, 'organisasi-{{ $index }}-deskripsi', 'organisasi[{{ $index }}][deskripsi_pekerjaan]')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Gunakan template kosong hanya jika tidak ada data -->
        {!! str_replace('{id}', 0, $organisasiEmptyTemplate) !!}
        @php $organisasiCount = 1; @endphp
    @endif
</div>