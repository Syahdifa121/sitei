<div class="form-section expanded mb-3" id="organisasi-{id}">
    <div class="section-header" onclick="toggleSection('organisasi-{id}')">
        <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
        <span class="section-title flex-grow-1">Organisasi {id}</span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOrganisasi({id})" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="section-content" style="padding: 16px;">
        <input type="hidden" name="organisasi[{id}][urutan]" class="urutan-field" value="{id}">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Organisasi</label>
                <input type="text" class="form-control rounded-3" name="organisasi[{id}][nama_organisasi]" placeholder="contoh: Himpunan Mahasiswa Teknik">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Posisi</label>
                <input type="text" class="form-control rounded-3" name="organisasi[{id}][posisi]" placeholder="contoh: Ketua Divisi IT">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Lokasi Organisasi</label>
                <input type="text" class="form-control rounded-3" name="organisasi[{id}][lokasi_organisasi]" placeholder="contoh: Jakarta, Indonesia">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Deskripsi Organisasi</label>
                <textarea class="form-control rounded-3" name="organisasi[{id}][deskripsi_organisasi]" rows="2" placeholder="Jelaskan singkat tentang organisasi..."></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Mulai</label>
                <input type="date" class="form-control rounded-3" name="organisasi[{id}][tanggal_mulai]">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Selesai</label>
                <input type="date" class="form-control rounded-3" name="organisasi[{id}][tanggal_selesai]">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="organisasi[{id}][masih_aktif]" value="1">
                    <label class="form-check-label">Masih aktif</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-0">
                <label class="form-label fw-bold">Deskripsi Pekerjaan</label>
                <div class="list-input-container">
                    <ul class="list-group mb-2" id="organisasi-{id}-deskripsi-list"></ul>
                    <input type="text" class="form-control rounded-3" id="organisasi-{id}-deskripsi-input" 
                           placeholder="Masukkan deskripsi pekerjaan, tekan Enter untuk menambah..." 
                           onkeypress="handleListInput(event, 'organisasi-{id}-deskripsi', 'organisasi[{id}][deskripsi_pekerjaan]')">
                </div>
            </div>
        </div>
    </div>
</div>