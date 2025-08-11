<div class="row">
    <!-- Grafik Pekerjaan Utama Alumni (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Pekerjaan Utama Alumni
                    @if ($totalAlumni > 0)
                        (Total: {{ $totalAlumni }})
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if ($totalAlumni > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="pekerjaanUtamaChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pekerjaanUtama as $status => $persentase)
                                            <tr>
                                                <td>{{ $status }}</td>
                                                <td>{{ number_format($persentase, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data alumni yang tersedia untuk ditampilkan.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Masa Tunggu (Pie Kecil) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Masa Tunggu Mendapatkan Pekerjaan</h5>
            </div>
            <div class="card-body">
                @if (count($masaTunggu) > 0)
                    <div class="row">
                        <div class="col-12 col-md-4 text-center">
                            <canvas id="masaTungguChart" style="max-width: 400px;"></canvas>
                        </div>
                        <div class="col-12 col-md-8 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Masa Tunggu</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($masaTunggu as $masa => $persentase)
                                            <tr>
                                                <td>{{ $masa }}</td>
                                                <td>{{ number_format($persentase, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data masa tunggu pekerjaan yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Jenis Perusahaan/Instansi (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Jenis Perusahaan/Instansi Tempat Bekerja</h5>
            </div>
            <div class="card-body">
                @if (count($jenisPerusahaan) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="jenisPerusahaanChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Jenis</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jenisPerusahaan as $jenis => $persen)
                                            <tr>
                                                <td>{{ $jenis }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data jenis perusahaan/instansi yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Tingkat Tempat Kerja -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Tingkat Perusahaan/Instansi Tempat Bekerja</h5>
            </div>
            <div class="card-body">
                @if (count($tingkatTempatKerja) > 0)
                    <div class="row">
                        <div class="col-12 col-md-4 text-center">
                            <canvas id="tingkatTempatKerjaChart" style="max-width: 400px;"></canvas>
                        </div>
                        <div class="col-12 col-md-8 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tingkat</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tingkatTempatKerja as $tingkat => $persentase)
                                            <tr>
                                                <td>{{ $tingkat }}</td>
                                                <td>{{ number_format($persentase, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data tingkat pekerjaan yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Penghasilan Alumni -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Penghasilan Alumni</h5>
            </div>
            <div class="card-body">
                @if($totalAlumniBekerja > 0)
                    <div class="row">
                        <div class="chart-container col-md-6" style="height: 400px;">
                            <canvas id="penghasilanChart"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Range Penghasilan</th>
                                            <th>Persentase</th>
                                            <th>Jumlah Alumni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penghasilanAlumni as $range => $data)
                                            <tr>
                                                <td>{{ $range }}</td>
                                                <td>{{ number_format($data['persentase'], 2) }}%</td>
                                                <td>{{ $data['jumlah'] }} orang</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data penghasilan alumni yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Jabatan Dalam Bekerja (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Jabatan Dalam Bekerja/Berwiraswasta</h5>
            </div>
            <div class="card-body">
                @if (count($jabatanAlumni) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="jabatanAlumniChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Jabatan</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jabatanAlumni as $jabatan => $persen)
                                            <tr>
                                                <td>{{ $jabatan }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data jabatan alumni yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Sumber Biaya (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Sumber Biaya Perkuliahan</h5>
            </div>
            <div class="card-body">
                @if (count($sumberPembiayaan) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="sumberPembiayaanChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sumber</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sumberPembiayaan as $sumber => $persen)
                                            <tr>
                                                <td>{{ $sumber }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data sumber pembiayaan yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Hubungan Studi dengan Pekerjaan (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Hubungan Bidang Studi dengan Pekerjaan</h5>
            </div>
            <div class="card-body">
                @if (count($hubunganStudiPekerjaan) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="hubunganStudiPekerjaanChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($hubunganStudiPekerjaan as $kategori => $persen)
                                            <tr>
                                                <td>{{ $kategori }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data hubungan studi dengan pekerjaan yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Tingkat Pendidikan Paling Tepat Untuk Pekerjaan (Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Tingkat Pendidikan Paling Tepat Untuk Pekerjaan</h5>
            </div>
            <div class="card-body">
                @if (count($pendidikanSesuaiPekerjaan) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="pendidikanSesuaiPekerjaanChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tingkat</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pendidikanSesuaiPekerjaan as $tingkat => $persen)
                                            <tr>
                                                <td>{{ $tingkat }}</td>
                                                <td>{{ number_format($persen, 2) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data tingkat pendidikan sesuai pekerjaan yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Metode Pembelajaran (Horizontal Stacked Bar Chart) -->
    <div class="col-md-12 mb-4">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Pelaksanaan Metode Pembelajaran di Program Studi</h5>
            </div>
            <div class="card-body">
                @if (isset($metodePembelajaran) && count($metodePembelajaran['labels']) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="metodePembelajaranChart" height="400"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Metode Pembelajaran</th>
                                            @foreach($metodePembelajaran['datasets'] as $dataset)
                                                <th class="text-center">{{ $dataset['label'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($metodePembelajaran['labels'] as $index => $label)
                                            <tr>
                                                <td>{{ $label }}</td>
                                                @foreach($metodePembelajaran['datasets'] as $dataset)
                                                    <td class="text-center">{{ $dataset['data'][$index] }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data metode pembelajaran yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Grafik Kompetensi Alumni -->
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-card-gradient text-white p-3">
                <h5 class="mb-0">Kompetensi Alumni</h5>
            </div>
            <div class="card-body">
                @if (isset($kompetensiAlumni) && count($kompetensiAlumni) > 0)
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="kompetensiRadarChart" height="300"></canvas>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="table-responsive w-100">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Kompetensi</th>
                                            <th class="text-center">Rata-rata Saat Lulus (A)</th>
                                            <th class="text-center">Rata-rata Saat Ini (B)</th>
                                            <th class="text-center">Selisih (B-A)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kompetensiAlumni as $kompetensi)
                                            <tr>
                                                <td>{{ $kompetensi['kompetensi'] }}</td>
                                                <td class="text-center">{{ $kompetensi['rata_lulus'] }}</td>
                                                <td class="text-center">{{ $kompetensi['rata_saat_ini'] }}</td>
                                                <td class="text-center {{ $kompetensi['selisih'] > 0 ? 'text-success' : ($kompetensi['selisih'] < 0 ? 'text-danger' : '') }}">
                                                    {{ $kompetensi['selisih'] > 0 ? '+' : '' }}{{ $kompetensi['selisih'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data kompetensi yang tersedia.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<style>
    .bg-card-gradient {
        background: linear-gradient(to right, #3875B6, #37C3F4);
    }
</style>

<script>
    @if ($totalAlumni > 0)
        // Grafik Pekerjaan Utama (Bar Chart Horizontal)
        new Chart(document.getElementById('pekerjaanUtamaChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($pekerjaanUtama)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($pekerjaanUtama)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // membuatnya horizontal
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (count($masaTunggu) > 0)
        // Grafik Masa Tunggu (Pie Chart)
        new Chart(document.getElementById('masaTungguChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($masaTunggu)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($masaTunggu)) !!},
                    backgroundColor: ['#66BB6A', '#EF5350'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (count($jenisPerusahaan) > 0)
        // Grafik Jenis Perusahaan/Instansi (Bar Chart Horizontal)
        new Chart(document.getElementById('jenisPerusahaanChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($jenisPerusahaan)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($jenisPerusahaan)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (count($tingkatTempatKerja) > 0)
        // Grafik Tingkat Tempat Kerja (Pie Chart)
        new Chart(document.getElementById('tingkatTempatKerjaChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($tingkatTempatKerja)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($tingkatTempatKerja)) !!},
                    backgroundColor: ['#66BB6A', '#EF5350', '#FFC107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if($totalAlumniBekerja > 0)
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('penghasilanChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($penghasilanAlumni)) !!},
                    datasets: [{
                        label: 'Persentase Alumni',
                        data: {!! json_encode(array_column($penghasilanAlumni, 'persentase')) !!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw;
                                    const jumlah = {!! json_encode(array_column($penghasilanAlumni, 'jumlah')) !!}[context.dataIndex];
                                    return [
                                        `${label}: ${value.toFixed(2)}%`,
                                        `Jumlah: ${jumlah} orang`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Persentase (%)'
                            },
                            ticks: {
                                callback: function (value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Range Penghasilan'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    @endif

    @if (count($jabatanAlumni) > 0)
        // Grafik Jenis Perusahaan/Instansi (Bar Chart Horizontal)
        new Chart(document.getElementById('jabatanAlumniChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($jabatanAlumni)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($jabatanAlumni)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (count($sumberPembiayaan) > 0)
        // Grafik Jenis Perusahaan/Instansi (Bar Chart Horizontal)
        new Chart(document.getElementById('sumberPembiayaanChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($sumberPembiayaan)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($sumberPembiayaan)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (count($hubunganStudiPekerjaan) > 0)
        new Chart(document.getElementById('hubunganStudiPekerjaanChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($hubunganStudiPekerjaan)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($hubunganStudiPekerjaan)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        },
                        title: {
                            display: true,
                            text: 'Persentase'
                        }
                    },
                    x: {
                        ticks: {
                            autoSkip: false
                        }
                    }
                }
            }
        });
    @endif

    @if (count($pendidikanSesuaiPekerjaan) > 0)
        // Grafik Jenis Perusahaan/Instansi (Bar Chart Horizontal)
        new Chart(document.getElementById('pendidikanSesuaiPekerjaanChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($pendidikanSesuaiPekerjaan)) !!},
                datasets: [{
                    label: 'Persentase',
                    data: {!! json_encode(array_values($pendidikanSesuaiPekerjaan)) !!},
                    backgroundColor: [
                        '#36A2EB', '#FFCE56', '#4BC0C0', '#FF6384', '#9966FF'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.label}: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + "%";
                            }
                        }
                    }
                }
            }
        });
    @endif

    @if (isset($metodePembelajaran) && count($metodePembelajaran['labels']) > 0)
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('metodePembelajaranChart').getContext('2d');
            
            // Warna untuk setiap skala penilaian (sesuai gambar)
            const backgroundColors = [
                '#4BC0C0', // 1 (Sangat Besar)
                '#36A2EB', // 2 (Besar)
                '#FFCE56', // 3 (Cukup Besar)
                '#FF9F40', // 4 (Kurang)
                '#FF6384'  // 5 (Tidak Sama Sekali)
            ];
            
            // Siapkan datasets
            const datasets = [];
            @foreach($metodePembelajaran['datasets'] as $index => $dataset)
                datasets.push({
                    label: '{{ $dataset["label"] }}',
                    data: {!! json_encode($dataset["data"]) !!},
                    backgroundColor: backgroundColors[{{ $index }}],
                    borderColor: '#fff',
                    borderWidth: 1
                });
            @endforeach
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($metodePembelajaran['labels']) !!},
                    datasets: datasets
                },
                options: {
                    indexAxis: 'x', // Membuat chart horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Jumlah Responden'
                            },
                            ticks: {
                                stepSize: 5
                            }
                        },
                        y: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value}`;
                                }
                            }
                        }
                    }
                }
            });
        });
    @endif

    @if (isset($kompetensiRadar) && !empty($kompetensiRadar['labels']))
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('kompetensiRadarChart').getContext('2d');
            
            // Pastikan data tidak null/undefined
            const labels = {!! json_encode($kompetensiRadar['labels'] ?? []) !!};
            const rataLulus = {!! json_encode($kompetensiRadar['rata_lulus'] ?? []) !!};
            const rataSaatIni = {!! json_encode($kompetensiRadar['rata_saat_ini'] ?? []) !!};

            if (labels.length === 0 || rataLulus.length === 0 || rataSaatIni.length === 0) {
                console.error("Data grafik tidak valid!");
                return;
            }

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Saat Lulus (A)',
                            data: rataLulus,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Saat Ini (B)',
                            data: rataSaatIni,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        r: {
                            min: 1,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    @endif

</script>