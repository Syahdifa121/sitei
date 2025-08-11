<form action="{{ route('mahasiswa.update-profil') }}" method="POST" enctype="multipart/form-data" class="form-step active"
    id="biodata-step">
    @csrf

    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Data Diri</h5>
        </div>
        <div class="card-body p-4">
            <div id="profil-form">

                <div class="row flex-column-reverse flex-md-row">
                    <div class="col-md-12 mb-3">
                        <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-3 @error('nama') is-invalid @enderror"
                            id="nama" name="nama" value="{{ old('nama', $profil->nama ?? $user->nama) }}">
                        @error('nama')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="text" class="form-control rounded-3 @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $profil->email ?? $user->email) }}">
                        @error('email')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="no_telepon" class="form-label fw-bold">Nomor Telepon</label>
                        <input type="number" class="form-control rounded-3 @error('no_telepon') is-invalid @enderror"
                            id="no_telepon" name="no_telepon"
                            value="{{ old('no_telepon', $profil->no_telepon ?? null) }}">
                        @error('no_telepon')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="linkedin" class="form-label fw-bold">LinkedIn <span class="text-muted"
                                style="font-size: 12px">Profile URL</span></label>
                        <input type="text" class="form-control rounded-3 @error('linkedin') is-invalid @enderror"
                            id="linkedin" name="linkedin" value="{{ old('linkedin', $profil->linkedin ?? null) }}">
                        @error('linkedin')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="portfolio" class="form-label fw-bold">Portfolio/Website <span class="text-muted"
                                style="font-size: 12px">URL</span></label>
                        <input type="text" class="form-control rounded-3 @error('portfolio') is-invalid @enderror"
                            id="portfolio" name="portfolio" value="{{ old('portfolio', $profil->portfolio ?? null) }}">
                        @error('portfolio')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label for="deskripsi_diri" class="form-label fw-bold">Deskripsi singkat tentang kamu</label>
                        <textarea class="form-control rounded-3 @error('deskripsi_diri') is-invalid @enderror" name="deskripsi_diri"
                            rows="5"
                            placeholder="Masukkan deskripsi singkat tentang diri Anda, misalnya: Lulusan baru di bidang Teknik Informatika, yang selalu memperluas pengalaman dan pengetahuan. Seorang yang memiliki minat di bidang web development, seperti pengembangan aplikasi web dengan teknologi javascript">{{ old('deskripsi_diri', $profil->deskripsi_diri ?? null) }}</textarea>
                        @error('deskripsi_diri')
                            <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Pengalaman</h5>
        </div>
        <div class="card-body p-4">
            <div id="pengalaman-form">
                <div class="pengalaman-list" id="pengalaman-list">
                    <!-- Pengalaman items will be dynamically added here -->
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPengalaman()">
                    <i class="fas fa-plus me-2"></i>Tambah Pengalaman
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Pendidikan</h5>
        </div>
        <div class="card-body p-4">
            <div id="pendidikan-form">
                <div class="pendidikan-list" id="pendidikan-list">
                    <!-- Pendidikan items will be dynamically added here -->
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPendidikan()">
                    <i class="fas fa-plus me-2"></i>Tambah Pendidikan
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Organisasi</h5>
        </div>
        <div class="card-body p-4">
            <div id="organisasi-form">
                <div class="organisasi-list" id="organisasi-list">
                    <!-- Organisasi items will be dynamically added here -->
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOrganisasi()">
                    <i class="fas fa-plus me-2"></i>Tambah Organisasi
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-3 mb-4">
        <div class="card-header bg-card-gradient p-3 rounded-top-3">
            <h5 class="card-title mb-0 fw-bold text-white">Skill</h5>
        </div>
        <div class="card-body p-4">
            <div id="skill-form">
                <div class="mb-3">
                    <label for="hard_skill" class="form-label fw-bold">Hard Skills</label>
                    <textarea class="form-control rounded-3 @error('hard_skill') is-invalid @enderror" name="hard_skill" rows="4"
                        placeholder="Contoh: Laravel, PHP, JavaScript, MySQL, React, dll.">{{ old('hard_skill', $profil->hard_skill ?? null) }}</textarea>
                    @error('hard_skill')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="soft_skill" class="form-label fw-bold">Soft Skills</label>
                    <textarea class="form-control rounded-3 @error('soft_skill') is-invalid @enderror" name="soft_skill" rows="4"
                        placeholder="Contoh: Komunikasi, Teamwork, Problem Solving, Leadership, dll.">{{ old('soft_skill', $profil->soft_skill ?? null) }}</textarea>
                    @error('soft_skill')
                        <span class="text-danger" style="font-size: 11px">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Button Perbarui Data -->
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-end">
            <button type="submit" id="btn-perbarui" class="btn btn-primary px-4 py-2 rounded-3">
                <i class="fas fa-save me-2"></i>Perbarui Data
            </button>
        </div>
    </div>
</form>

<script>
    function previewImage(input) {
        const uploadContainer = document.getElementById('upload-container');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const currentPhoto = document.getElementById('current-photo');
        const previewImg = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                // Hide placeholder or current photo
                if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
                if (currentPhoto) currentPhoto.style.display = 'none';

                // Show preview
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';

                // Update container style
                uploadContainer.style.border = '2px solid #28a745';
                uploadContainer.style.backgroundColor = 'transparent';
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            // Reset to original state
            if (uploadPlaceholder) uploadPlaceholder.style.display = 'flex';
            if (currentPhoto) currentPhoto.style.display = 'block';
            previewImg.style.display = 'none';

            // Reset container style
            uploadContainer.style.border = currentPhoto ? '2px solid #28a745' : '2px dashed #007bff';
            uploadContainer.style.backgroundColor = currentPhoto ? 'transparent' : 'rgba(0, 123, 255, 0.05)';
        }
    }

    // Add hover effect
    document.addEventListener('DOMContentLoaded', function() {
        const uploadContainer = document.getElementById('upload-container');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const currentPhoto = document.getElementById('current-photo');
        const previewImg = document.getElementById('preview-img');

        uploadContainer.addEventListener('mouseenter', function() {
            const isShowingPreview = previewImg && previewImg.style.display === 'block';
            const hasPhoto = currentPhoto && currentPhoto.style.display !== 'none';

            if (!isShowingPreview && !hasPhoto) {
                this.style.backgroundColor = 'rgba(0, 123, 255, 0.1)';
                this.style.borderColor = '#0056b3';
            }
        });

        uploadContainer.addEventListener('mouseleave', function() {
            const isShowingPreview = previewImg && previewImg.style.display === 'block';
            const hasPhoto = currentPhoto && currentPhoto.style.display !== 'none';

            if (!isShowingPreview && !hasPhoto) {
                this.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
                this.style.borderColor = hasPhoto ? '#28a745' : '#007bff';
            }
        });
    });
</script>

<script>
    let pengalamanCount = {{ $pengalaman ? count($pengalaman) : 0 }};
    let pendidikanCount = {{ $pendidikan ? count($pendidikan) : 0 }};
    let organisasiCount = {{ $organisasi ? count($organisasi) : 0 }};

    // Variabel untuk melacak item yang sedang expanded
    let currentExpandedPengalaman = null;
    let currentExpandedPendidikan = null;
    let currentExpandedOrganisasi = null;

    // Templates
    const pengalamanTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.pengalaman')->render()) !!}`;
    const pengalamanEmptyTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.pengalaman-empty')->render()) !!}`;
    const pendidikanTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.pendidikan')->render()) !!}`;
    const pendidikanEmptyTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.pendidikan-empty')->render()) !!}`;
    const organisasiTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.organisasi')->render()) !!}`;
    const organisasiEmptyTemplate = `{!! str_replace(["\r", "\n"], '', view('alumni.mahasiswa.profil-alumni.template.organisasi-empty')->render()) !!}`;

    // Inisialisasi saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        if (pengalamanCount === 0) {
            addPengalaman();
        } else {
            listPengalaman();
        }

        if (pendidikanCount === 0) {
            addPendidikan();
        } else {
            listPendidikan();
        }

        if (organisasiCount === 0) {
            addOrganisasi();
        } else {
            listOrganisasi();
        }

        initDragAndDrop();
    });
    
    // Pengalaman Functions
    function listPengalaman() {
        const pengalamanList = document.getElementById('pengalaman-list');

        const pengalamanItem = document.createElement('div');
        pengalamanItem.innerHTML = pengalamanTemplate.replace(/{id}/g, pengalamanCount);
        pengalamanList.appendChild(pengalamanItem.firstElementChild);
        initDragAndDrop();
    }

    function addPengalaman() {
        // Collapse currently expanded pengalaman if any
        if (currentExpandedPengalaman) {
            collapseSection(currentExpandedPengalaman);
        }
        
        pengalamanCount++;
        const pengalamanList = document.getElementById('pengalaman-list');

        const pengalamanItem = document.createElement('div');
        pengalamanItem.innerHTML = pengalamanEmptyTemplate.replace(/{id}/g, pengalamanCount);
        const newSection = pengalamanItem.firstElementChild;
        
        // Make the new section expanded by default
        const content = newSection.querySelector('.section-content');
        if (content) {
            content.style.display = 'block';
            newSection.classList.remove('collapsed');
            newSection.classList.add('expanded');
        }
        
        pengalamanList.appendChild(newSection);
        
        // Update tracking variable to the new section
        currentExpandedPengalaman = `pengalaman-${pengalamanCount}`;
        
        // Initialize drag and drop for the new element
        initDragAndDrop();
    }

    function deletePengalaman(id) {
        document.getElementById(`pengalaman-${id}`).remove();
    }

    // Pendidikan Functions
    function listPendidikan() {
        const pendidikanList = document.getElementById('pendidikan-list');

        const pendidikanItem = document.createElement('div');
        pendidikanItem.innerHTML = pendidikanTemplate.replace(/{id}/g, pendidikanCount);
        pendidikanList.appendChild(pendidikanItem.firstElementChild);
        initDragAndDrop();
    }

    function addPendidikan() {
        // Collapse currently expanded pendidikan if any
        if (currentExpandedPendidikan) {
            collapseSection(currentExpandedPendidikan);
        }
        
        pendidikanCount++;
        const pendidikanList = document.getElementById('pendidikan-list');

        const pendidikanItem = document.createElement('div');
        pendidikanItem.innerHTML = pendidikanEmptyTemplate.replace(/{id}/g, pendidikanCount);
        const newSection = pendidikanItem.firstElementChild;
        
        // Make the new section expanded by default
        const content = newSection.querySelector('.section-content');
        if (content) {
            content.style.display = 'block';
            newSection.classList.remove('collapsed');
            newSection.classList.add('expanded');
        }
        
        pendidikanList.appendChild(newSection);
        
        // Update tracking variable to the new section
        currentExpandedPendidikan = `pendidikan-${pendidikanCount}`;

        // Initialize drag and drop for the new element
        initDragAndDrop();
    }

    function deletePendidikan(id) {
        document.getElementById(`pendidikan-${id}`).remove();
    }

    // Organisasi Functions
    function listOrganisasi() {
        const organisasiList = document.getElementById('organisasi-list');

        const organisasiItem = document.createElement('div');
        organisasiItem.innerHTML = organisasiTemplate.replace(/{id}/g, organisasiCount);
        organisasiList.appendChild(organisasiItem.firstElementChild);
        initDragAndDrop();
    }

    function addOrganisasi() {
        if (currentExpandedOrganisasi) {
            collapseSection(currentExpandedOrganisasi);
        }
        
        organisasiCount++;
        const organisasiList = document.getElementById('organisasi-list');

        const organisasiItem = document.createElement('div');
        organisasiItem.innerHTML = organisasiEmptyTemplate.replace(/{id}/g, organisasiCount); // ← pakai template kosong
        const newSection = organisasiItem.firstElementChild;
        
        // Make the new section expanded by default
        const content = newSection.querySelector('.section-content');
        if (content) {
            content.style.display = 'block';
            newSection.classList.remove('collapsed');
            newSection.classList.add('expanded');
        }
        
        organisasiList.appendChild(newSection);
        
        // Update tracking variable to the new section
        currentExpandedOrganisasi = `organisasi-${organisasiCount}`;

        // Initialize drag and drop for the new element
        initDragAndDrop();
    }

    function deleteOrganisasi(id) {
        document.getElementById(`organisasi-${id}`).remove();
    }

    function handleListInput(event, listId, inputName) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const input = event.target;
            const value = input.value.trim();

            if (value) {
                const listElement = document.getElementById(listId + '-list');
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
                
                // Create a counter for the portfolio items
                const itemCount = listElement.children.length;
                
                listItem.innerHTML = `
                    <span>${value}</span>
                    <div>
                        <input type="hidden" name="${inputName}[${itemCount}]" value="${value}">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeListItem(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                listElement.appendChild(listItem);
                input.value = '';
            }
        }
    }

    function removeListItem(button) {
        const listItem = button.closest('li');
        listItem.remove();
        
        // Reindex remaining items
        const listElement = listItem.parentNode;
        Array.from(listElement.children).forEach((item, index) => {
            const input = item.querySelector('input');
            if (input) {
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            }
        });
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const content = section.querySelector('.section-content');
        const isExpanded = section.classList.contains('expanded');
        
        // Determine section type from sectionId
        const sectionType = sectionId.split('-')[0]; // 'pengalaman', 'pendidikan', or 'organisasi'
        
        if (isExpanded) {
            // Collapse current section
            content.style.display = 'none';
            section.classList.remove('expanded');
            section.classList.add('collapsed');
            
            // Update tracking variable
            if (sectionType === 'pengalaman') {
                currentExpandedPengalaman = null;
            } else if (sectionType === 'pendidikan') {
                currentExpandedPendidikan = null;
            } else if (sectionType === 'organisasi') {
                currentExpandedOrganisasi = null;
            }
        } else {
            // First, collapse any currently expanded section of the same type
            if (sectionType === 'pengalaman' && currentExpandedPengalaman) {
                collapseSection(currentExpandedPengalaman);
            } else if (sectionType === 'pendidikan' && currentExpandedPendidikan) {
                collapseSection(currentExpandedPendidikan);
            } else if (sectionType === 'organisasi' && currentExpandedOrganisasi) {
                collapseSection(currentExpandedOrganisasi);
            }
            
            // Expand current section
            content.style.display = 'block';
            section.classList.remove('collapsed');
            section.classList.add('expanded');
            
            // Update tracking variable
            if (sectionType === 'pengalaman') {
                currentExpandedPengalaman = sectionId;
            } else if (sectionType === 'pendidikan') {
                currentExpandedPendidikan = sectionId;
            } else if (sectionType === 'organisasi') {
                currentExpandedOrganisasi = sectionId;
            }
        }
    }

    // Helper function to collapse a section
    function collapseSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            const content = section.querySelector('.section-content');
            if (content) {
                content.style.display = 'none';
                section.classList.remove('expanded');
                section.classList.add('collapsed');
            }
        }
    }

    // Drag and Drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        initDragAndDrop();
    });

    function initDragAndDrop() {
        let draggedElement = null;

        document.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('drag-handle')) {
                draggedElement = e.target.closest('.form-section');
                draggedElement.style.opacity = '0.5';
                e.preventDefault();
            }
        });

        document.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (draggedElement) {
                const afterElement = getDragAfterElement(draggedElement.parentNode, e.clientY);
                if (afterElement == null) {
                    draggedElement.parentNode.appendChild(draggedElement);
                } else {
                    draggedElement.parentNode.insertBefore(draggedElement, afterElement);
                }
            }
        });

        document.addEventListener('mouseup', function() {
            if (draggedElement) {
                draggedElement.style.opacity = '1';
                updateUrutan(draggedElement.parentNode);
                draggedElement = null;
            }
        });
    }

    function updateUrutan(container) {
        const items = container.querySelectorAll('.form-section');
        items.forEach((item, index) => {
            const urutanField = item.querySelector('.urutan-field');
            if (urutanField) {
                urutanField.value = index + 1;
            }
        });
    }

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.form-section:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return {
                    offset: offset,
                    element: child
                };
            } else {
                return closest;
            }
        }, {
            offset: Number.NEGATIVE_INFINITY
        }).element;
    }
</script>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    }

    .btn-primary:disabled {
        transform: none;
        box-shadow: 0 2px 10px rgba(0, 123, 255, 0.2);
    }

    .form-section {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: #fff;
        transition: all 0.3s ease;
    }

    .form-section.expanded {
        border-color: #007bff;
        box-shadow: 0 2px 10px rgba(0, 123, 255, 0.1);
    }

    .form-section.collapsed {
        background: #f8f9fa;
        border-color: #dee2e6;
    }

    .form-section.collapsed .section-content {
        display: none;
    }

    .section-header {
        padding: 12px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        user-select: none;
        border-bottom: 1px solid #e0e0e0;
    }

    .form-section.collapsed .section-header {
        border-bottom: none;
    }

    .section-header:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }

    .drag-handle {
        font-size: 14px;
    }

    .drag-handle:hover {
        color: #007bff !important;
    }

    .section-title {
        font-weight: 600;
        color: #333;
    }

    .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        border-color: #007bff;
    }

    .list-input-container .list-group-item {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .list-input-container .list-group-item:hover {
        background-color: #e9ecef;
    }
</style>