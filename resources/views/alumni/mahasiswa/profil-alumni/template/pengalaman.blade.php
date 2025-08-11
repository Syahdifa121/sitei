@php
    use App\Models\Profil;

    $user = Auth::guard('mahasiswa')->user();
    $profil = Profil::where('user_nim', $user->nim)->first();
    $pengalaman = json_decode($profil->pengalaman ?? '[]', true);
    $pengalamanEmptyTemplate = view('alumni.mahasiswa.profil-alumni.template.pengalaman-empty')->render();
@endphp

<div class="pengalaman-list" id="pengalaman-list">
    @if($pengalaman)
        @foreach($pengalaman as $index => $item)
            <div class="form-section collapsed mb-3" id="pengalaman-{{ $index }}">
                <div class="section-header" onclick="toggleSection('pengalaman-{{ $index }}')">
                    <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
                    <span class="section-title flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        Pengalaman {{ $index + 1 }} - {{ $item['nama_perusahaan'] }}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePengalaman({{ $index }})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="section-content" style="padding: 16px; display: none;">
                    <input type="hidden" name="pengalaman[{{ $index }}][urutan]" class="urutan-field" value="{{ $index }}">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Perusahaan</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pengalaman[{{ $index }}][nama_perusahaan]" 
                                   value="{{ $item['nama_perusahaan'] ?? '' }}" 
                                   placeholder="contoh: PT. Tech Indonesia">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pengalaman[{{ $index }}][jabatan]" 
                                   value="{{ $item['jabatan'] ?? '' }}" 
                                   placeholder="contoh: Frontend Developer">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Lokasi Perusahaan</label>
                            <input type="text" class="form-control rounded-3" 
                                   name="pengalaman[{{ $index }}][lokasi_perusahaan]" 
                                   value="{{ $item['lokasi_perusahaan'] ?? '' }}" 
                                   placeholder="contoh: Jakarta, Indonesia">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Deskripsi Perusahaan</label>
                            <textarea class="form-control rounded-3" 
                                      name="pengalaman[{{ $index }}][deskripsi_perusahaan]" 
                                      rows="2" 
                                      placeholder="Jelaskan singkat tentang perusahaan...">{{ $item['deskripsi_perusahaan'] ?? '' }}</textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="pengalaman[{{ $index }}][tanggal_mulai]" 
                                   value="{{ $item['tanggal_mulai'] ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control rounded-3" 
                                   name="pengalaman[{{ $index }}][tanggal_selesai]" 
                                   value="{{ $item['tanggal_selesai'] ?? '' }}">
                            <div class="form-check mt-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="pengalaman[{{ $index }}][masih_bekerja]" 
                                       value="1"
                                       @if(!empty($item['masih_bekerja']) && $item['masih_bekerja'] == 1) checked @endif>
                                <label class="form-check-label">Masih bekerja disini</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label class="form-label fw-bold">Portofolio & Prestasi</label>
                            <div class="list-input-container">
                                <ul class="list-group mb-2" id="pengalaman-{{ $index }}-portofolio-list">
                                    @if(!empty($item['portofolio_prestasi']))
                                        @foreach($item['portofolio_prestasi'] as $prestasiIndex => $prestasi)
                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                <span>{{ $prestasi }}</span>
                                                <div>
                                                    <input type="hidden" 
                                                           name="pengalaman[{{ $index }}][portofolio_prestasi][]" 
                                                           value="{{ $prestasi }}">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeListItem(this)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <input type="text" class="form-control rounded-3" 
                                       id="pengalaman-{{ $index }}-portofolio-input" 
                                       placeholder="Masukkan prestasi atau portofolio, tekan Enter untuk menambah..." 
                                       onkeypress="handleListInput(event, 'pengalaman-{{ $index }}-portofolio', 'pengalaman[{{ $index }}][portofolio_prestasi]')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <!-- Gunakan template kosong hanya jika tidak ada data -->
        {!! str_replace('{id}', 0, $pengalamanEmptyTemplate) !!}
        @php $pengalamanCount = 1; @endphp
    @endif
</div>