@php
    use App\Models\Profil;

    $user = Auth::guard('mahasiswa')->user();
    $profil = $user ? Profil::where('user_nim', $user->nim)->first() : null;
@endphp

<div class="card shadow-sm rounded-3">
    <div class="card-header bg-card-gradient p-3 rounded-top-3">
        <h5 class="card-title mb-0 fw-bold text-white">Preview CV</h5>
    </div>
    <div class="card-body">
        @if(!$user)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Anda harus login terlebih dahulu.
            </div>
        @elseif(!$profil)
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>Profil belum lengkap. Silakan lengkapi terlebih dahulu.
            </div>
        @elseif($profil && !$profil->cv_path )
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>CV belum digenerate. Silakan klik tombol di bawah.
            </div>
            <button id="generateCv" class="btn btn-success">
                <i class="fas fa-file-alt me-2"></i>Generate CV
            </button>
        @else
            <div class="mb-3" style="height: 500px;">
                <iframe src="{{ Storage::url($profil->cv_path) }}" width="100%" height="100%" style="border: none;"></iframe>
            </div>
            <div class="d-flex justify-content-between">
                <button id="refreshCv" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i>Perbarui Data CV
                </button>
                <a href="{{ route('alumni.download-cv') }}" class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Download CV
                </a>
            </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        function processCV(buttonId, route) {
            $(buttonId).click(function () {
                const button = $(this);
                const originalText = button.html();
                
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function () {
                        button.prop('disabled', true)
                            .html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
                    },
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            if (response.message) {
                                alert(response.message);
                            }
                            // Reload page after short delay
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        } else {
                            alert('Gagal memproses CV: ' + (response.message || 'Unknown error'));
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Terjadi kesalahan. Silahkan coba lagi.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 500) {
                            errorMessage = 'Internal Server Error. Periksa log untuk detail.';
                        }
                        
                        alert(errorMessage);
                        console.error('AJAX Error:', xhr);
                    },
                    complete: function () {
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });
        }

        // Only bind events if buttons exist
        if ($('#generateCv').length) {
            processCV('#generateCv', '{{ route("alumni.generate-cv") }}');
        }
        
        if ($('#refreshCv').length) {
            processCV('#refreshCv', '{{ route("alumni.generate-cv") }}');
        }
    });
</script>