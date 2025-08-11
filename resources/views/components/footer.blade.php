<footer class="footer mt-5" style="background-color: #343a40; color: #fff; padding: 12px 0; position: relative; bottom: 0; width: 100%;">
    <div class="container text-center">
        @php
            $tab = request()->get('tab');
            $developer = match ($tab) {
                'statistik-alumni' => 'Adhitya Zanev Putra',
                'profil' => 'Adhitya Zanev Putra',
                'performa-alumni' => 'Adhitya Zanev Putra',
                'form-alumni' => 'Adhitya Zanev Putra',
                'data-alumni' => 'Adhitya Zanev Putra',
                'sebaran-alumni' => 'Muhammad Syah Difa Lubis',
                default => 'Syahirah Tri Meilina',
            };
        @endphp

        <p class="mb-0">
            Dikembangkan oleh Mahasiswa Prodi Teknik Informatika UNRI 
            (<span style="color: #A1E3F9;">{{ $developer }}</span>)
        </p>
    </div>
</footer>
