<!-- Default empty template (expanded) when no data exists -->
<div class="form-section expanded mb-3" id="pengalaman-{id}">
    <div class="section-header" onclick="toggleSection('pengalaman-{id}')">
        <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
        <span class="section-title flex-grow-1">Pengalaman {id}</span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePengalaman({id})" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="section-content" style="padding: 16px;">
        <input type="hidden" name="pengalaman[{id}][urutan]" class="urutan-field" value="{id}">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Perusahaan</label>
                <input type="text" class="form-control rounded-3" name="pengalaman[{id}][nama_perusahaan]" placeholder="contoh: PT. Tech Indonesia">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Jabatan</label>
                <input type="text" class="form-control rounded-3" name="pengalaman[{id}][jabatan]" placeholder="contoh: Frontend Developer">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Lokasi Perusahaan</label>
                <input type="text" class="form-control rounded-3" name="pengalaman[{id}][lokasi_perusahaan]" placeholder="contoh: Jakarta, Indonesia">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Deskripsi Perusahaan</label>
                <textarea class="form-control rounded-3" name="pengalaman[{id}][deskripsi_perusahaan]" rows="2" placeholder="Jelaskan singkat tentang perusahaan..."></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Mulai</label>
                <input type="date" class="form-control rounded-3" name="pengalaman[{id}][tanggal_mulai]">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Selesai</label>
                <input type="date" class="form-control rounded-3" name="pengalaman[{id}][tanggal_selesai]">
                <div class="form-check mt-2">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="pengalaman[{id}][masih_bekerja]" 
                           value="1">
                    <label class="form-check-label">Masih bekerja disini</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-0">
                <label class="form-label fw-bold">Portofolio & Prestasi</label>
                <div class="list-input-container">
                    <ul class="list-group mb-2" id="pengalaman-{id}-portofolio-list"></ul>
                    <input type="text" class="form-control rounded-3" id="pengalaman-{id}-portofolio-input" placeholder="Masukkan prestasi atau portofolio, tekan Enter untuk menambah..." onkeypress="handleListInput(event, 'pengalaman-{id}-portofolio', 'pengalaman[{id}][portofolio_prestasi]')">
                </div>
            </div>
        </div>
    </div>
</div>