<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV - {{ $nama ?? 'Nama Lengkap' }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
            background: white;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            color: #333;
        }

        .contact-info {
            font-size: 11px;
            margin-bottom: 10px;
        }

        .contact-info span {
            margin: 0 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .description {
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .experience-item, .education-item, .organization-item {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .position-title {
            font-weight: bold;
            font-size: 13px;
            color: #333;
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .duration {
            font-size: 11px;
            color: #666;
            font-style: italic;
            text-align: right;
        }

        .location {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }

        .tech-stack {
            font-size: 11px;
            color: #444;
            font-style: italic;
            margin: 5px 0;
        }

        .description-text {
            margin-top: 8px;
            text-align: justify;
        }

        .description-text ul {
            margin-left: 15px;
            margin-top: 5px;
        }

        .description-text li {
            margin-bottom: 3px;
            line-height: 1.4;
        }

        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .skill-category {
            flex: 1;
            min-width: 250px;
        }

        .skill-category h4 {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .skill-list {
            line-height: 1.5;
        }

        /* Page break handling */
        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        /* Print-specific styles */
        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                padding: 15px;
            }
            
            .section {
                margin-bottom: 20px;
            }
        }

        /* ATS-friendly styles */
        .ats-friendly {
            background: none !important;
            color: #000 !important;
        }

        /* Ensure text is selectable and readable by ATS */
        .selectable {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
    </style>
</head>
<body class="ats-friendly selectable">
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <h1 class="name">{{ $nama }}</h1>
            <div class="contact-info">
                @if($no_telepon)
                    <span>{{ $no_telepon }}</span> |
                @endif
                @if($email)
                    <span>{{ $email }}</span>
                @endif
                @if($linkedin)
                    | <span>{{ $linkedin }}</span>
                @endif
                @if($portfolio)
                    | <span>{{ $portfolio }}</span>
                @endif
            </div>
        </div>

        <!-- Professional Summary -->
        @if($deskripsi_diri)
        <div class="section">
            <h2 class="section-title">Professional Summary</h2>
            <div class="description">
                {{ $deskripsi_diri }}
            </div>
        </div>
        @endif

        <!-- Work Experience -->
        @if(!empty($pengalaman))
        <div class="section">
            <h2 class="section-title">Work Experience</h2>
            @foreach($pengalaman as $exp)
                <div class="experience-item no-break">
                    <div class="item-header">
                        <div>
                            <div class="position-title">{{ $exp['jabatan'] ?? '' }}</div>
                            <div class="company-name">{{ $exp['nama_perusahaan'] ?? '' }}</div>
                            @if(!empty($exp['lokasi_perusahaan']))
                                <div class="location">{{ $exp['lokasi_perusahaan'] }}</div>
                            @endif
                        </div>
                        <div class="duration">
                            @if(!empty($exp['tanggal_mulai']))
                                {{ $exp['tanggal_mulai'] }} - 
                                @if(!empty($exp['masih_bekerja']) && $exp['masih_bekerja'] == 1)
                                    saat ini
                                @else
                                    {{ $exp['tanggal_selesai'] ?? '' }}
                                @endif
                            @endif
                        </div>
                    </div>
                    @if(!empty($exp['deskripsi_perusahaan']))
                        <div class="description-text">
                            <p>{{ $exp['deskripsi_perusahaan'] }}</p>
                        </div>
                    @endif
                    @if(!empty($exp['portofolio_prestasi']) && is_array($exp['portofolio_prestasi']))
                        <div class="description-text">
                            <ul>
                                @foreach($exp['portofolio_prestasi'] as $prestasi)
                                    @if(!empty($prestasi))
                                        <li>{{ $prestasi }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Education -->
        @if(!empty($pendidikan))
        <div class="section">
            <h2 class="section-title">Education</h2>
            @foreach($pendidikan as $edu)
                <div class="education-item no-break">
                    <div class="item-header">
                        <div>
                            <div class="position-title">{{ $edu['nama_pendidikan'] ?? '' }} - {{ $edu['lokasi_pendidikan'] ?? '' }}</div>
                            <div class="company-name">{{ $edu['tingkat_pendidikan'] ?? '' }}</div>
                            @if(!empty($edu['grade']))
                                <div class="location">GPA: {{ $edu['grade'] }}</div>
                            @endif
                        </div>
                        <div class="duration">
                            @if(!empty($edu['tanggal_mulai']) && !empty($edu['tanggal_selesai']))
                                {{ $edu['tanggal_mulai'] }} - {{ $edu['tanggal_selesai'] }}
                            @endif
                        </div>
                    </div>
                    @if(!empty($edu['aktivitas_pencapaian']) && is_array($edu['aktivitas_pencapaian']))
                        <div class="description-text">
                            <ul>
                                @foreach($edu['aktivitas_pencapaian'] as $aktivitas)
                                    @if(!empty($aktivitas))
                                        <li>{{ $aktivitas }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Organizational Experience -->
        @if(!empty($organisasi))
        <div class="section">
            <h2 class="section-title">Organizational Experience</h2>
            @foreach($organisasi as $org)
                <div class="organization-item no-break">
                    <div class="item-header">
                        <div>
                            <div class="position-title">{{ $org['posisi'] ?? '' }}</div>
                            <div class="company-name">{{ $org['nama_organisasi'] ?? '' }}</div>
                            @if(!empty($org['lokasi_organisasi']))
                                <div class="location">{{ $org['lokasi_organisasi'] }}</div>
                            @endif
                        </div>
                        <div class="duration">
                            @if(!empty($org['tanggal_mulai']))
                                {{ $org['tanggal_mulai'] }} -
                                @if(!empty($org['masih_aktif']) && $org['masih_aktif'] == 1)
                                    saat ini
                                @else
                                    {{ $org['tanggal_selesai'] ?? '' }}
                                @endif
                            @endif
                        </div>
                    </div>
                    @if(!empty($org['deskripsi_organisasi']))
                        <div class="description-text">
                            <p>{{ $org['deskripsi_organisasi'] }}</p>
                        </div>
                    @endif
                    @if(!empty($org['deskripsi_pekerjaan']) && is_array($org['deskripsi_pekerjaan']))
                        <div class="description-text">
                            <ul>
                                @foreach($org['deskripsi_pekerjaan'] as $desc)
                                    @if(!empty($desc))
                                        <li>{{ $desc }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Skills -->
        @if(!empty($hard_skill) || !empty($soft_skill))
            <div class="section">
                <h2 class="section-title">Skills</h2>
                <div class="skills-grid">
                    @if(!empty($hard_skill))
                        <div class="skill-category">
                            <h4>Technical Skills:</h4>
                            <div class="skill-list">
                                {{ implode(', ', $hard_skill) }}
                            </div>
                        </div>
                    @endif
                    @if(!empty($soft_skill))
                        <div class="skill-category">
                            <h4>Soft Skills:</h4>
                            <div class="skill-list">
                                {{ implode(', ', $soft_skill) }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
    <div style="position: fixed; left: 0; right: 0; bottom: 10px; width: 100%; text-align: center; font-size: 10px; color: #888; opacity: 0.6; z-index: 9999; pointer-events: none;">
        Created by Prodi Teknik Informatika Universitas Riau
    </div>
</body>
</html>