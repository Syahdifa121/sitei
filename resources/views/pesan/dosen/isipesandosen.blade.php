@extends('layouts.app')

@section('title', 'Isi Pesan Dosen')

@push('styles')
    <style>
    .btn-gradient a {
        color: white;
        text-decoration: none;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-gradient:hover a{
        color: black;
    }
    .main-content {
        padding: 20px 0 0 0;
    }
    .btn-kembali {
        background: linear-gradient(to right, #4ade80, #3b82f6);
        color: white;
        font-weight: bold;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-kembali:hover {
        background-color: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
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
    .teacher-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
        padding: 20px;
        margin-bottom: 20px;
        position: sticky;
        top: 90px;
    }
    .teacher-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid #28a745;
    }
    .teacher-info {
        text-align: center;
        margin-bottom: 20px;
    }
    .teacher-name {
        font-weight: bold;
        font-size: 20px;
        margin-bottom: 5px;
        color: #28a745;
    }
    .teacher-id {
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
    .info-table tr:last-child td {
        border-bottom: none;
    }
    .info-details {
        text-align: center;
        padding: 0 20px;
    }

    .info-details p {
        margin-bottom: 8px;  
        font-size: 14px;     
    }

    .info-details i {
        width: 20px;         
        margin-right: 8px;   
    }
    .btn-action {
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
    .chat-wrapper {
        display: flex;
        flex-direction: column;
    }
    .chat-container {
        padding-right: 10px;
        transition: all 0.3s ease;
    }
    .chat-container::-webkit-scrollbar {
        width: 5px;
    }
    .chat-container::-webkit-scrollbar-thumb {
        background-color: #007bff;
        border-radius: 10px;
    }
    .reply-form {
        margin-top: 20px;
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
        transition: all 0.3s ease;
    }
    .reply-form h4 {
        color: #007bff;
        margin-bottom: 15px;
    }
    .form-control {
        border-radius: 20px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
   
    
    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
    .priority-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        margin-top: 5px;
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
    .btn-kirim {
        background: linear-gradient(to right, #4ade80, #3b82f6);
        color: white;
        font-weight: bold;
        border: none;
        padding: 10px 25px;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .btn-kirim:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
        opacity: 0.9;
    }
</style>
@endpush
@section('content')
<div class="main-content">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="d-inline-block me-3 gradient-text">Isi Konsultasi</h2>
                <hr>
                <div class="mt-3">
                    <a href="{{ route('pesan.dashboardkonsultasi') }}" class="btn btn-kembali">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="teacher-card">
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Foto" class="teacher-photo mx-auto d-block">
                    <div class="teacher-info">
                        @if(auth()->guard('mahasiswa')->check())
                            <h3 class="teacher-name">{{ $pesan->dosen->nama }}</h3>
                            <p class="student-id">{{ $pesan->dosen->nip }}</p>
                            <div class="info-details">
                                <p class="mb-1"><i class="fas fa-graduation-cap me-2"></i> {{ $pesan->dosen->jurusan }}</p>
                            </div>
                        @else
                            <h3 class="teacher-name">{{ $pesan->mahasiswa->nama }}</h3>
                            <p class="student-id">{{ $pesan->mahasiswa->nim }}</p>
                            <div class="info-details">
                                <p class="mb-1"><i class="fas fa-graduation-cap me-2"></i> {{ $pesan->mahasiswa->jurusan }}</p>
                                <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i> Semester {{ $pesan->mahasiswa->semester }}</p>
                            </div>
                        @endif
                    </div>
                    <table class="info-table">
                        <tr>
                            <th>Pengirim</th>
                            <td>{{ $pesan->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIM</th>
                            <td>{{ $pesan->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <th>Subjek</th>
                            <td>{{ $pesan->subjek }}</td>
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
                    </table>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="chat-wrapper">
                    <div class="chat-container">
                        <!-- Pesan Utama -->
                        <div class="message-card student">
                            <div class="message-header">
                                <span class="name student">
                                    <i class="fas fa-user-circle"></i> {{ $pesan->mahasiswa->nama }}
                                </span>
                                <div>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> {{ $pesan->created_at->format('H:i, d F Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="message-body">
                                {!! nl2br(e($pesan->pesan)) !!}
                            </div>
                            @if($pesan->attachment)
                            <div class="attachment">
                                <p><i class="fas fa-paperclip"></i> Lampiran:</p>
                                <a href="{{ $pesan->attachment }}" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Lihat Lampiran
                                </a>
                            </div>
                            @endif
                        </div>

                        <!-- Balasan Pesan -->
                        @foreach($pesan->balasan as $balasan)
                        <div class="message-card {{ $balasan->role_id == 'mahasiswa' ? 'student' : 'teacher' }}">
                            <div class="message-header">
                                <span class="name {{ $balasan->role_id == 'mahasiswa' ? 'student' : 'teacher' }}">
                                    <i class="fas {{ $balasan->role_id == 'mahasiswa' ? 'fa-user-circle' : 'fa-user-tie' }}"></i>
                                    {{ $balasan->pengirim->nama }}
                                </span>
                                <div>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> {{ $balasan->created_at->format('H:i, d F Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="message-body">
                                {!! nl2br(e($balasan->pesan)) !!}
                            </div>
                            @if($balasan->attachment)
                            <div class="attachment">
                                <p><i class="fas fa-paperclip"></i> Lampiran:</p>
                                <a href="{{ route('pesan.attachment', $balasan->id) }}" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Lihat Lampiran
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Form Balas Pesan -->
                    @if($pesan->status === 'aktif')
                    <div class="reply-form">
                        <h4><i class="fas fa-reply"></i> Balas Pesan</h4>
                        <form id="replyForm" action="{{ route('pesan.reply', $pesan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control @error('pesan') is-invalid @enderror" 
                                        name="pesan" 
                                        rows="4" 
                                        placeholder="Tulis pesan Anda di sini..." 
                                        required></textarea>
                                @error('pesan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="file" 
                                    name="attachment" 
                                    class="form-control @error('attachment') is-invalid @enderror" 
                                    accept=".pdf,.doc,.docx">
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format yang diizinkan: PDF, DOC, DOCX (Max: 10MB)</small>
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('replyForm');
    if(form) {
        form.addEventListener('submit', async function(e) {
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
                
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan server');
                }
                
                // Jika berhasil
                if(data.success) {
                    // Optional: Tampilkan pesan sukses
                    alert('Pesan berhasil dikirim');
                    // Reload halaman
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
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
});
</script>
@endpush