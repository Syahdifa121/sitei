<div class="form-section expanded mb-3" id="pendidikan-{id}">
    <div class="section-header" onclick="toggleSection('pendidikan-{id}')">
        <i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: grab;"></i>
        <span class="section-title flex-grow-1">Pendidikan {id}</span>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePendidikan({id})" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <div class="section-content" style="padding: 16px;">
        <input type="hidden" name="pendidikan[{id}][urutan]" class="urutan-field" value="{id}">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Nama Pendidikan</label>
                <input type="text" class="form-control rounded-3" name="pendidikan[{id}][nama_pendidikan]" placeholder="contoh: Universitas Riau">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Lokasi Pendidikan</label>
                <input type="text" class="form-control rounded-3" name="pendidikan[{id}][lokasi_pendidikan]" placeholder="contoh: Riau, Indonesia">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Mulai</label>
                <input type="date" class="form-control rounded-3" name="pendidikan[{id}][tanggal_mulai]">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tanggal Selesai</label>
                <input type="date" class="form-control rounded-3" name="pendidikan[{id}][tanggal_selesai]">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Tingkat Pendidikan</label>
                <select class="form-control rounded-3" name="pendidikan[{id}][tingkat_pendidikan]">
                    <option value="">Pilih Tingkat</option>
                    <option value="SMA/SMK">SMA/SMK</option>
                    <option value="D3">Diploma 3</option>
                    <option value="S1">Sarjana (S1)</option>
                    <option value="S2">Magister (S2)</option>
                    <option value="S3">Doktor (S3)</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Grade/IPK <span class="text-muted" style="font-size: 12px">(opsional)</span></label>
                <input type="text" class="form-control rounded-3" name="pendidikan[{id}][grade]" placeholder="contoh: 3.75">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-0">
                <label class="form-label fw-bold">Aktivitas & Pencapaian</label>
                <div class="list-input-container">
                    <ul class="list-group mb-2" id="pendidikan-{id}-aktivitas-list"></ul>
                    <input type="text" class="form-control rounded-3" id="pendidikan-{id}-aktivitas-input" 
                           placeholder="Masukkan aktivitas atau pencapaian, tekan Enter untuk menambah..." 
                           onkeypress="handleListInput(event, 'pendidikan-{id}-aktivitas', 'pendidikan[{id}][aktivitas_pencapaian]')">
                </div>
            </div>
        </div>
    </div>
</div>