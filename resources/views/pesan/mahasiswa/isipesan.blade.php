@extends('layouts.app')

@section('title', 'Isi Pesan')

@push('styles')
<style>
    .gradient-text {
        background: linear-gradient(to right, #059669, #2563eb);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .btn-gradient {
        background: linear-gradient(to right, #4ade80, #3b82f6);
        border: none;
        color: white;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        z-index: 1;
        cursor: pointer;
    }
    .btn-gradient a {
        color: white;
        text-decoration: none;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-gradient:hover a {
        color: black;
    }
    .message-card {
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .message-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0,0,0,.15);
    }
    .message-card.student {
        border-left: 5px solid #28a745;
    }
    .message-card.teacher {
        border-left: 5px solid #007bff;
    }
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e0e0e0;
    }
    .message-header .name {
        font-weight: bold;
        font-size: 18px;
    }
    .message-header .name.student {
        color: #28a745;
    }
    .message-header .name.teacher {
        color: #007bff;
    }
    .message-body {
        font-size: 16px;
        color: #333;
    }
    .profile-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
        padding: 20px;
        margin-bottom: 20px;
        position: sticky;
        top: 90px;
    }
    .profile-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid #28a745;
    }
    .profile-info {
        text-align: center;
        margin-bottom: 20px;
    }
    .profile-name {
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 5px;
    }
    .profile-id {
        color: #6c757d;
        margin-bottom: 10px;
        font-size: 16px;
    }
    .info-table {
        width: 100%;
        margin: 20px 0;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
    }
    .info-table th, .info-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }
    .info-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }
    .priority-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .priority-high {
        background-color: #dc3545;
        color: white;
    }
    .priority-medium {
        background-color: #ffc107;
        color: black;
    }
    .priority-low {
        background-color: #28a745;
        color: white;
    }
    .reply-form {
        margin-top: 20px;
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
    .btn-kirim {
        background: linear-gradient(to right, #4ade80, #3b82f6);
        color: white;
        font-weight: bold;
        border: none;
        padding: 10px 25px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .status-ended {
        color: #dc3545;
        font-weight: bold;
    }
    .attachment {
        margin-top: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <h1 class="mb-2 gradient-text">Isi Konsultasi</h1>
    <hr>
    <button class="btn btn-gradient mb-4">
        <a href="{{ route('pesan.dashboardkonsultasi') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>

    <div class="row">
        <!-- Sidebar Profile Card -->
        <div class="col-md-4">
            <div class="profile-card text-center">
                @php
                    $profileUser = auth()->guard('mahasiswa')->check() ? $pesan->dosen : $pesan->mahasiswa;
                    $isStudent = auth()->guard('mahasiswa')->check();
                    $borderColor = $isStudent ? '#007bff' : '#28a745';
                @endphp

                <img src="{{ $profileUser->foto ? asset('storage/foto_profil/' . $profileUser->foto) : asset('images/default-avatar.png') }}" 
                     alt="Profile Photo" 
                     class="profile-photo mx-auto d-block"
                     style="border-color: {{ $borderColor }}">

                <div class="profile-info">
                    <h3 class="profile-name" style="color: {{ $borderColor }}">{{ $profileUser->nama }}</h3>
                    <p class="profile-id">{{ $isStudent ? 'NIP. ' . $profileUser->nip : 'NIM. ' . $profileUser->nim }}</p>
                </div>

                <table class="info-table">
                    <tr>
                        <th>Subjek</th>
                        <td>{{ $pesan->subjek }}</td>
                    </tr>
                    <tr>
                        <th>{{ $isStudent ? 'Penerima' : 'Pengirim' }}</th>
                        <td>{{ $isStudent ? $pesan->dosen->nama : $pesan->mahasiswa->nama }}</td>
                    </tr>
                    <tr>
                        <th>Prioritas</th>
                        <td>
                            <span class="priority-badge {{ $pesan->prioritas == 'mendesak' ? 'priority-high' : 'priority-medium' }}">
                                {{ ucfirst($pesan->prioritas) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Dikirim</th>
                        <td>{{ $pesan->created_at->format('H:i, d F Y') }}</td>
                    </tr>
                    @if($pesan->status === 'selesai')
                    <tr id="statusRow">
                        <th>Status</th>
                        <td class="status-ended">Pesan telah berakhir</td>
                    </tr>
                    @endif
                </table>

                @if($pesan->status === 'aktif')
                    @if(auth()->guard('mahasiswa')->check())
                        <button class="btn btn-danger btn-action" id="endChatBtn" data-pesan-id="{{ $pesan->id }}">
                            <i class="fas fa-times-circle"></i> Akhiri Pesan
                        </button>
                    @endif
                @else
                    <button class="btn btn-secondary btn-action" disabled>
                        <i class="fas fa-check-circle"></i> Pesan Diakhiri
                    </button>
                @endif
            </div>
        </div>

        <!-- Chat Section -->
        <div class="col-md-8">
            <div class="chat-wrapper">
                <!-- Original Message -->
                <div class="message-card {{ auth()->guard('mahasiswa')->check() ? 
                    ($pesan->mahasiswa_nim ? 'student' : 'teacher') : 
                    ($pesan->dosen_nip ? 'teacher' : 'student') }}">
                    <div class="message-header">
                        <span class="name {{ auth()->guard('mahasiswa')->check() ? 
                            ($pesan->mahasiswa_nim ? 'student' : 'teacher') : 
                            ($pesan->dosen_nip ? 'teacher' : 'student') }}">
                            <i class="fas {{ $pesan->mahasiswa_nim ? 'fa-user-circle' : 'fa-user-tie' }}"></i>
                            {{ $pesan->mahasiswa_nim ? $pesan->mahasiswa->nama : $pesan->dosen->nama }}
                        </span>
                        <small class="text-muted">
                            <i class="far fa-clock"></i> {{ $pesan->created_at->format('H:i, d F Y') }}
                        </small>
                    </div>
                    <div class="message-body">
                        {!! nl2br(e($pesan->pesan)) !!}
                    </div>
                    @if($pesan->attachment)
                    <div class="attachment">
                        <p>
                            <i class="fab fa-google-drive"></i> Lampiran:
                        </p>
                        <a href="{{ $pesan->attachment }}" class="btn btn-sm btn-primary" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-external-link-alt"></i> Buka File di Google Drive
                        </a>
                    </div>
                    @endif
                </div>


                <!-- Replies -->
                @foreach($pesan->balasan as $balasan)
                <div class="message-card {{ $balasan->role_id == '2' ? 'student' : 'teacher' }}">
                    <div class="message-header">
                        <span class="name {{ $balasan->role_id == '2' ? 'student' : 'teacher' }}">
                            <i class="fas {{ $balasan->role_id == '2' ? 'fa-user-circle' : 'fa-user-tie' }}"></i>
                            @if($balasan->role_id == 2)
                                {{ optional($pesan->mahasiswa)->nama ?? 'Mahasiswa' }}
                            @else
                                {{ optional($pesan->dosen)->nama ?? 'Dosen' }}
                            @endif
                        </span>
                        <small class="text-muted">
                            <i class="far fa-clock"></i> {{ $balasan->created_at->format('H:i, d F Y') }}
                        </small>
                    </div>
                    <div class="message-body">
                        {!! nl2br(e($balasan->pesan)) !!}
                    </div>
                    @if($balasan->attachment)
                    <div class="attachment">
                        <p>
                            <i class="fab fa-google-drive"></i> Lampiran:
                        </p>
                        <a href="{{ $balasan->attachment }}" class="btn btn-sm btn-primary" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-external-link-alt"></i> Buka File di Google Drive
                        </a>
                    </div>
                    @endif
                </div>
                @endforeach

                <!-- Reply Form -->
                @if($pesan->status === 'aktif')
                <div class="reply-form">
                    <h4 class="mb-3"><i class="fas fa-reply"></i> Balas Pesan</h4>
                    <form action="{{ route('pesan.reply', $pesan->id) }}" method="POST" id="replyMessageForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control @error('pesan') is-invalid @enderror" 
                                    name="pesan" 
                                    rows="4" 
                                    placeholder="Tulis pesan Anda di sini..." 
                                    required>{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Ganti bagian input file yang lama -->
                        <div class="mb-3">
                            <label for="attachment" class="form-label">
                                <i class="fab fa-google-drive"></i> Link Google Drive (Opsional)
                            </label>
                            <input type="url" 
                                id="attachment" 
                                name="attachment" 
                                class="form-control @error('attachment') is-invalid @enderror" 
                                placeholder="https://drive.google.com/file/d/..."
                                value="{{ old('attachment') }}"
                            >
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Pastikan link dapat diakses oleh publik
                            </small>
                        </div>
                        <button type="submit" class="btn btn-kirim">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- End Chat Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Akhiri Pesan</h5>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengakhiri pesan ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmEndChat">Ya, Akhiri Pesan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const endChatBtn = document.getElementById('endChatBtn');
    const confirmEndChatBtn = document.getElementById('confirmEndChat');
    const replyForm = document.getElementById('replyMessageForm');
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    
    // Handle message submission
    if(replyForm) {
        replyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            try {
                // Disable button & show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
                
                const formData = new FormData(this);
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if(data.success) {
                    // Reset form and reload page to show new message
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat mengirim pesan');
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan saat mengirim pesan');
            } finally {
                // Restore button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }

    // Handle ending the chat
    if(endChatBtn) {
        endChatBtn.addEventListener('click', function() {
            modal.show();
        });

        confirmEndChatBtn.addEventListener('click', async function() {
            try {
                const pesanId = endChatBtn.dataset.pesanId;
                const response = await fetch(`/pesan/end/${pesanId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if(data.success) {
                    // Update UI to reflect ended state
                    if(replyForm) {
                        replyForm.closest('.reply-form').style.display = 'none';
                    }
                    
                    // Update end chat button
                    endChatBtn.innerHTML = '<i class="fas fa-check-circle"></i> Pesan Diakhiri';
                    endChatBtn.classList.remove('btn-danger');
                    endChatBtn.classList.add('btn-secondary');
                    endChatBtn.disabled = true;

                    // Add status row if it doesn't exist
                    const infoTable = document.querySelector('.info-table');
                    if(infoTable) {
                        const statusRow = document.getElementById('statusRow') || document.createElement('tr');
                        if(!document.getElementById('statusRow')) {
                            statusRow.id = 'statusRow';
                            statusRow.innerHTML = `
                                <th>Status</th>
                                <td class="status-ended">Pesan telah berakhir</td>
                            `;
                            infoTable.querySelector('tbody').appendChild(statusRow);
                        }
                    }

                    modal.hide();
                    
                } else {
                    throw new Error(data.message || 'Gagal mengakhiri pesan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan saat mengakhiri pesan');
            }
        });
    }

    // Auto-scroll to bottom of chat
    const chatContainer = document.querySelector('.chat-container');
    if(chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // File input validation
    const fileInput = document.querySelector('input[type="file"]');
    if(fileInput) {
        fileInput.addEventListener('change', function() {
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if(this.files[0]) {
                if(this.files[0].size > maxSize) {
                    alert('Ukuran file tidak boleh lebih dari 10MB');
                    this.value = '';
                    return;
                }
                
                if(!allowedTypes.includes(this.files[0].type)) {
                    alert('Format file harus PDF, DOC, atau DOCX');
                    this.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endpush