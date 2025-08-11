@extends('layouts.app')

@section('title', 'Masukkan Jadwal Bimbingan')

@push('styles')
<link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ================ EXTERNAL EVENT ================ */
        .fc-event.external-event {
            background-color: #f6c5f6 !important;
            color: #431b3f !important; 
            border: none !important;
            font-style: italic;
            font-weight: 600;
        }


        .fc-event {
        background-color: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin-bottom: 4px !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
        font-size: 13px;
        font-weight: 500;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        }

        .fc-event-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: #1a73e8;
        border-radius: 50%;
        margin-right: 6px;
        flex-shrink: 0;
        }

        .fc-event-time,
        .fc-event-title {
        display: inline;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 13px;
        color: #1a1a1a;
        }

        .fc-event-time {
        margin-right: 4px;
        color: #1a73e8;
        font-weight: 500;
        }

        .fc-daygrid-event {
        background-color: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin-bottom: 4px !important;
        box-shadow: none !important;
        display: flex;
        align-items: center;
        font-size: 13px;
        font-weight: 500;
        color: #1a1a1a;
        }

        /* Tampilan untuk event di day view */
        .fc-timeGridDay-view .fc-event,
        .fc-timeGridWeek-view .fc-event {
            background-color: #1a73e8 !important;
            color: white !important;
            border-left: none !important;
        }

        .fc-timeGridDay-view .fc-event-title,
        .fc-timeGridWeek-view .fc-event-title {
            color: white !important;
        }

        .fc-timeGridDay-view .fc-event-time,
        .fc-timeGridWeek-view .fc-event-time {
            color: white !important;
        }

        .fc-popover {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background-color: #f0f4fb;
        padding: 10px;
        z-index: 1050 !important;
        }

        .fc-popover .fc-event {
        background: none !important;
        border: none !important;
        color: #1a1a1a !important;
        }

        @media (max-width: 768px) {
        .fc-event, .fc-event * {
            font-size: 0.5rem !important;
        }
        }


        .fc-scroller {
            overflow: hidden !important;
        }


        /* Base Styles */
        :root {
            /* Brand Colors */
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;

            /* Neutral Colors */
            --neutral-50: #f8fafc;
            --neutral-100: #f1f5f9;
            --neutral-200: #e2e8f0;
            --neutral-300: #cbd5e1;
            --neutral-400: #94a3b8;
            --neutral-500: #64748b;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1e293b;

            /* Event Colors */
            --event-blue: #e0f2fe;
            --event-red: #fee2e2;
            --event-green: #dcfce7;
            --event-yellow: #fef3c7;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        /* ================ CONTAINER & LAYOUT ================ */
        #calendar {
            background: white;
        }

        .calendar-container {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            padding: 24px;
            margin: 0 auto;
            max-width: none; /* Ubah ini dari 1200px */
            width: 100%; /* Tambah ini */
            height: 100%; /* Ubah ini dari 100% */
            min-height: 800px; /* Tambah ini */
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ================ TOOLBAR & NAVIGATION ================ */
        .fc .fc-toolbar {
            position: relative;
            margin-bottom: 32px !important;
            padding: 16px 24px;
            background: var(--neutral-50);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .fc .fc-toolbar-title {
            font-size: 24px !important;
            font-weight: 600;
            color: var(--neutral-800);
            letter-spacing: -0.025em;
        }

        /* Button Styling */
        .fc .fc-button {
            border-radius: 12px !important;
            font-weight: 500 !important;
            height: 40px !important;
            padding: 0 20px !important;
            font-size: 14px !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: var(--shadow-md) !important;
        }

        .fc .fc-button-primary {
            background: white !important;
            border: 1px solid var(--neutral-200) !important;
            color: var(--neutral-700) !important;
        }

        .fc .fc-button-primary:hover {
            background: var(--neutral-50) !important;
            border-color: var(--neutral-300) !important;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md) !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active:hover {
            background: var(--primary-dark) !important;
        }

        /* Button Groups */
        .fc-button-group {
            box-shadow: var(--shadow-sm);
            border-radius: 12px;

            gap: 1px;
        }

        .fc-button-group .fc-button {
            border-radius: 0 !important;
            margin: 0 !important;
        }

        .fc-button-group .fc-button:first-child {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .fc-button-group .fc-button:last-child {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }

        /* ================ CALENDAR HEADER ================ */
        .fc-theme-standard th {
            padding: 8px 0 4px 0 !important;
            background: white;
        }

        .fc-col-header-cell-cushion {
            padding: 4px !important;
            color: var(--neutral-600) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em;
            text-decoration: none !important;
        }

        /* ================ CALENDAR GRID ================ */
        .fc-theme-standard td,
        .fc-theme-standard th {
            border: 1px solid var(--neutral-200) !important;
        }


        /* Date Cell Styling ukuran grid kalender*/
        .fc .fc-daygrid-body {
            width: 100% !important;
            height: auto !important;
        }
        .fc .fc-daygrid-day-frame {
            min-height: 120px !important;
            padding: 8px !important;
        }

        .fc .fc-daygrid-day-top {
            justify-content: center !important;
            padding-top: 0px !important;
        }

        .fc-view-harness,
        .fc-view-harness-active,
        .fc-view {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .fc .fc-daygrid-day-number {
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: var(--neutral-700) !important;
            text-decoration: none !important;
            border-radius: 50% !important;
            transition: all 0.2s ease;
        }

        /* Today Styling */
        .fc .fc-day-today {
            background: var(--event-blue) !important;
        }

        .fc .fc-day-today .fc-daygrid-day-number {
            background: var(--primary-dark) !important;
            color: white !important;
            font-weight: 600 !important;
        }

        /* Weekend Days */
        .fc-day-sat,
        .fc-day-sun {
            background: var(--neutral-50) !important;
        }

        /* Other Month Days */
        .fc-day-other {
            background: var(--neutral-50) !important;
        }

        .fc-day-other .fc-daygrid-day-number {
            color: var(--neutral-400) !important;

        }

        /* ================ EVENTS STYLING ================ */
        .fc-event {
            border: none !important;
            padding: 4px 8px !important;
            margin: 2px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
            font-weight: 500 !important;
            box-shadow: var(--shadow-md) !important;
            background-color: #161D6F;
            transition: all 0.2s ease-in-out !important;
            color: var(--neutral-50) !important;
        }

        .fc-event:hover {
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: var(--shadow-md) !important;
            color: var(--neutral-800) !important;
        }


        /* Tersedia Styling */
        .small,
        small {
            color: #A0E4CB;
            transition: color 0.1s ease-in-out;
        }

        .small:hover {
            color: #17594A !important;
        }


        /* Event Types */
        .fc-event-krs {
            background: var(--event-blue) !important;
            color: #0369a1 !important;
            border-left: 3px solid #0284c7 !important;
        }

        .fc-event-kp {
            background: var(--event-red) !important;
            color: #b91c1c !important;
            border-left: 3px solid #dc2626 !important;
        }

        .fc-event-mbkm {
            background: var(--event-yellow) !important;
            color: #92400e !important;
            border-left: 3px solid #d97706 !important;
        }

        .fc-event-skripsi {
            background: var(--event-green) !important;
            color: #166534 !important;
            border-left: 3px solid #16a34a !important;
        }
        .fc-event-konsultasi {
            background: var(--event-yellow) !important;
            color: #92400e !important;
            border-left: 3px solid #f4b400 !important;
        }
        .fc-event-lainnya {
            background: #f3f4f6 !important;
            color: #374151 !important;
            border-left: 3px solid #9ca3af !important;
        }

        /* ================ MODAL STYLING ================ */
        .modal-content {
            border: none;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--neutral-200);
            background: var(--neutral-50);
            border-radius: 24px 24px 0 0;
        }

        .modal-header .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--neutral-800);
        }

        .modal-body {
            padding: 24px;
        }

        /* Form Elements */
        .form-label {
            font-weight: 500;
            color: var(--neutral-700);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid var(--neutral-300);
            padding: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }



        /* ================ MORE EVENTS LINK ================ */
        .fc-daygrid-more-link {
            color: var(--primary) !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            text-decoration: none !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            background: var(--neutral-50) !important;
            transition: all 0.2s ease !important;
        }

        .fc-daygrid-more-link:hover {
            background: var(--neutral-100) !important;
            color: var(--primary-dark) !important;
        }

        /* ================ LOADING STATE ================ */
        .fc-loading {
            position: relative;
        }

        .fc-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 3px solid var(--neutral-200);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        /* ================ RESPONSIVE DESIGN ================ */
        @media (max-width: 768px) {
            .calendar-container {
                padding: 16px;
                margin: 16px;
                border-radius: 16px;
            }

            .fc .fc-toolbar {
                flex-direction: column;
                padding: 16px;
                gap: 12px;
            }

            .fc .fc-toolbar-title {
                font-size: 20px !important;
            }

            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .fc .fc-button {
                padding: 0 16px !important;
                height: 36px !important;
                font-size: 13px !important;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 80px !important;
            }
        }

        /* ================ ANIMATIONS ================ */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fc-event-new {
            animation: fadeIn 0.3s ease-out;
        }

        /* Legend Styling */
        .calendar-legend {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 8px;
        }

        .swal2-popup {
            padding: 1.5em;
        }

        .swal2-html-container {
            text-align: center !important;
            margin: 1em 0;
        }

        /* Styling untuk container detail */
        .detail-container {
            text-align: left;
            padding: 10px 0;
        }

        /* Styling untuk setiap item detail */
        .detail-item {
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item strong {
            color: #1a73e8;
            font-weight: 600;
            font-size: 0.9em;
        }

        .detail-item span {
            color: #333;
            padding-left: 4px;
        }

        /* Styling untuk tombol */
        .swal2-confirm.swal2-styled {
            padding: 0.5em 2em;
            font-weight: 500;
        }

        .swal2-cancel.swal2-styled {
            padding: 0.5em 2em;
            font-weight: 500;
        }

        /* Info Box Styling */
        .info-box {
            background-color: #e8f0fe;
            border: 1px solid #1a73e8;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .info-box p {
            color: #1967d2;
            margin-bottom: 10px;
        }

        .info-box .btn-connect {
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
        }

        .info-box .btn-connect:hover {
            background-color: #1557b0;
        }

        .guide-box {
            display: none;
            width: 100%;
            max-width: 100%;
            margin-bottom: 25px;
            animation: slideUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            position: relative;
            }
            
            @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
            }
            
        .card-header {
            background: linear-gradient(-135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            }
            
        .card-header:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05),
                rgba(255, 255, 255, 0.05) 10px,
                rgba(255, 255, 255, 0.02) 10px,
                rgba(255, 255, 255, 0.02) 20px
            );
            transform: rotate(30deg);
            z-index: 0;
            }
            
        .card-header h5 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            position: relative;
            z-index: 1;
            }
            
        .card-header i {
            font-size: 24px;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.15);
            height: 40px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            position: relative;
            z-index: 1;
            }
            
        .card-body {
            padding: 30px;
            position: relative;
            }
            
            .card-body:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 40%;
            background: url('/api/placeholder/500/500') no-repeat center center;
            background-size: cover;
            opacity: 0.03;
            z-index: 0;
            }
            
            .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
            position: relative;
            z-index: 1;
            }
            
            .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
            }
            
            h6 {
            font-size: 16px;
            margin-bottom: 20px;
            color: var(--dark);
            font-weight: 700;
            display: flex;
            align-items: center;
            position: relative;
            padding-left: 15px;
            }
            
            h6:before {
            content: '';
            position: absolute;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
            }
            
            .guide-steps {
            counter-reset: step-counter;
            list-style-type: none;
            margin: 0;
            padding: 0 0 0 15px;
            }
            
            .guide-steps li {
            position: relative;
            margin-bottom: 18px;
            padding-left: 40px;
            padding-bottom: 5px;
            transition: all 0.3s;
            }
            
            .guide-steps li:hover {
            transform: translateX(5px);
            }
            
            .guide-steps li:before {
            counter-increment: step-counter;
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: -2px;
            width: 28px;
            height: 28px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
            }
            
            .guide-steps li:after {
            content: '';
            position: absolute;
            left: 14px;
            top: 28px;
            bottom: 0;
            width: 1px;
            background: rgba(67, 97, 238, 0.3);
            }
            
            .guide-steps li:last-child:after {
            display: none;
            }
            
            .guide-info {
            counter-reset: step-counter;
            list-style-type: none;
            margin: 0;
            padding: 0 0 0 15px;
            }
            
            .guide-info li {
            position: relative;
            margin-bottom: 18px;
            padding-left: 40px;
            padding-bottom: 5px;
            transition: all 0.3s;
            }
        
            .guide-info li:hover {
            transform: translateX(5px);
            }
            
            .guide-info li:before {
            counter-increment: step-counter;
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: -2px;
            width: 28px;
            height: 28px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(67, 97, 238, 0.3);
            }
            
            .guide-info li:after {
            content: '';
            position: absolute;
            left: 14px;
            top: 28px;
            bottom: 0;
            width: 1px;
            background: rgba(67, 97, 238, 0.3);
            }

            .guide-info li:last-child:after {
            display: none;
            }
            
            .text-end {
            text-align: right;
            margin-top: 30px;
            position: relative;
            z-index: 1;
            }
            
            #hideGuide {
            padding: 8px 18px;
            background: transparent;
            color: var(--dark);
            border: 2px solid #dee2e6;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            outline: none;
            display: inline-flex;
            align-items: center;
            }
            
            #hideGuide:hover {
            background: var(--light);
            border-color: #ced4da;
            transform: translateY(-2px);
            }
            
            #hideGuide i {
            margin-left: 8px;
            font-size: 12px;
            }
            
            @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 30px;
            }
            
            .col-md-6:last-child {
                margin-bottom: 0;
            }
            
            .card-body {
                padding: 20px;
            }
        }
    
        /* Style untuk form check switch */
        .form-check-input {
            cursor: pointer;
        }

        .form-check-label {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #kuotaContainer, #jenisBimbinganContainer {
            transition: all 0.3s ease;
        }

        /* Compact SweetAlert Popup Styles */
.compact-swal-popup {
    border-radius: 12px !important;
    padding: 0 !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}

.compact-swal-title {
    padding: 16px 16px 0 16px !important;
    margin-bottom: 0 !important;
}

.compact-swal-button {
    border-radius: 6px !important;
    padding: 8px 16px !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
}

.compact-swal-button:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.swal2-html-container {
    padding: 16px !important;
    margin: 0 !important;
}

/* Fix untuk jarak button */
.compact-swal-actions,
.swal2-actions {
    padding: 12px 24px 16px 24px !important; /* Kurangi padding untuk jarak yang lebih dekat */
    margin: 0 !important;
}

/* Animation for popup appearance */
.swal2-show {
    animation: swal2-show 0.2s ease-out !important;
}

@keyframes swal2-show {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Hover effect for info rows */
.info-row {
    transition: all 0.2s ease;
}

.info-row:hover {
    transform: translateX(2px);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .compact-swal-popup {
        width: 95% !important;
        margin: 0 auto !important;
    }
}
    </style>
@endpush

@section('content')
<div class="container mt-4">
    <h1 class="mb-2 gradient-text fw-bold">Masukkan Jadwal</h1>
    <hr>
    <button class="btn btn-gradient mb-4 mt-2 d-flex align-items-center justify-content-center">
        <a href="{{ url('/persetujuan') }}">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </button>
    
    @if(!$isConnected)
    <div class="info-box">
        <p class="mb-2">Untuk menggunakan fitur ini, Anda perlu memberikan izin akses ke Google Calendar dengan email: <strong>{{ $email }}</strong></p>
        <a href="{{ route('dosen.google.connect') }}" class="btn btn-connect">
            <i class="fas fa-calendar-plus"></i>
            Hubungkan dengan Google Calendar
        </a>
    </div>
    @else
    <!-- Tambahkan Kotak Panduan Pengguna di sini -->
    <div class="guide-box">
        <div class="card-header">
          <i class="fas fa-info-circle"></i>
          <h5>Panduan Penggunaan Kalender</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>Cara Menambahkan Jadwal</h6>
              <ol class="guide-steps">
                <li>Klik pada tanggal yang diinginkan di kalender</li>
                <li>Masukkan waktu mulai dan waktu selesai bimbingan</li>
                <li>Tambahkan catatan jika diperlukan</li>
                <li>Klik tombol "Simpan Jadwal"</li>
              </ol>
            </div>
            <div class="col-md-6">
              <h6>Informasi Penting</h6>
              <ul class="guide-info">
                <li>Jadwal hanya dapat dibuat pada hari kerja (Senin-Jumat)</li>
                <li>Jam operasional bimbingan: 08:00 - 18:00</li>
                <li>Durasi minimum bimbingan adalah 30 menit</li>
                <li>Klik pada jadwal untuk melihat detail atau menghapusnya</li>
              </ul>
            </div>
          </div>
          <div class="text-end">
            <button id="hideGuide">
              Sembunyikan Panduan
              <i class="fas fa-chevron-up"></i>
            </button>
          </div>
        </div>
      </div>
    
    <div class="calendar-container">
        <div id="calendar" class="w-100"></div>
    </div>
@endif
</div>


<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="h4 gradient-text fw-bold">Tambah Jadwal Bimbingan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control" id="startTime" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control" id="endTime" required>
                            </div>
                        </div>
                        <div id="timeValidationFeedback"></div>
                        <small class="text-muted mt-2 d-block">Jadwal tersedia pada jam kerja (08:00 - 18:00)<br>Durasi minimum bimbingan adalah 30 menit</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableKuota">
                            <label class="form-check-label" for="enableKuota" id="kuotaLabel">Batasi Kuota Bimbingan</label>
                        </div>
                        <div id="kuotaContainer" class="mt-2" style="display: none;">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="bi bi-people-fill"></i>
                                </span>
                                <input type="number" class="form-control" id="kuotaBimbingan" min="1" value="1" placeholder="Jumlah maksimal mahasiswa">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableJenisBimbingan" name="enableJenisBimbingan">
                            <label class="form-check-label" for="enableJenisBimbingan" id="jenisBimbinganLabel">Tentukan Jenis Bimbingan</label>
                        </div>
                        <div id="jenisBimbinganContainer" class="mt-2" style="display: none;">
                            <select class="form-select" id="jenisBimbingan" name="jenis_bimbingan">
                                <option value="" selected disabled>- Pilih Jenis Bimbingan -</option>
                                <option value="skripsi">Bimbingan Skripsi</option>
                                <option value="kp">Bimbingan KP</option>
                                <option value="akademik">Bimbingan Akademik</option>
                                <option value="konsultasi">Konsultasi Pribadi</option>
                                <option value="mbkm">Bimbingan MBKM</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-gradient" id="saveEvent">Simpan Jadwal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/id.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/id.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrfToken) {
        console.error('CSRF token tidak ditemukan');
        tampilkanPesan('error', 'Terjadi kesalahan sistem. Silakan muat ulang halaman.');
        return;
    }

    let calendar;
    let selectedDate = null;

    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Elemen kalender tidak ditemukan');
        return;
    }

    // Style untuk label toggle
    const inactiveStyle = "color: #6c757d; font-weight: normal;";
    const activeStyle = "color: #212529; font-weight: bold;";

    function formatDateTime(date) {
        return moment(date).format('DD MMM YYYY HH:mm');
    }

    const tampilkanPesan = (icon, text) => {
        Swal.fire({
            icon: icon,
            text: text,
            confirmButtonColor: '#1a73e8'
        });
    };

    // Fungsi untuk menangani tampilan panduan
    function handleGuideVisibility() {
        const guideBox = document.querySelector('.guide-box');
        const showGuideButton = document.getElementById('showGuideButton');
        
        if (!guideBox) return;
        
        // Periksa apakah user baru terhubung dari Google Calendar
        const urlParams = new URLSearchParams(window.location.search);
        const justConnected = urlParams.get('connected') === 'true';
        
        // Jika user baru terhubung, tampilkan panduan dan hapus preferensi tersembunyi
        if (justConnected) {
            guideBox.style.display = 'block';
            localStorage.removeItem('calendarGuideHidden');
            return;
        }
        
        // Jika user sudah pernah menyembunyikan panduan, jangan tampilkan
        if (localStorage.getItem('calendarGuideHidden') === 'true') {
            guideBox.style.display = 'none';
            if (showGuideButton) {
                showGuideButton.style.display = 'block';
            }
        } else {
            // Jika belum pernah disembunyikan, tampilkan panduan
            guideBox.style.display = 'block';
        }
    }
    
    // Setup tombol untuk menampilkan kembali panduan
    function setupShowGuideButton() {
        // Cek apakah tombol sudah ada
        if (!document.getElementById('showGuideButton')) {
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'show-guide-button-container';
            buttonContainer.style.cssText = 'position: fixed; right: 20px; bottom: 20px; z-index: 1000;';
            
            const showGuideButton = document.createElement('button');
            showGuideButton.id = 'showGuideButton';
            showGuideButton.className = 'btn btn-primary btn-sm';
            showGuideButton.innerHTML = '<i class="fas fa-question-circle"></i> Tampilkan Panduan';
            showGuideButton.style.cssText = 'box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: none;';
            
            buttonContainer.appendChild(showGuideButton);
            document.body.appendChild(buttonContainer);
            
            showGuideButton.addEventListener('click', function() {
                const guideBox = document.querySelector('.guide-box');
                if (guideBox) {
                    // Tampilkan panduan dengan animasi
                    guideBox.style.display = 'block';
                    guideBox.style.animation = 'fadeIn 0.3s ease-out forwards';
                    
                    // Sembunyikan tombol tampilkan panduan
                    showGuideButton.style.display = 'none';
                    
                    // Hapus preferensi tersembunyi dari localStorage
                    localStorage.removeItem('calendarGuideHidden');
                }
            });
        }
    }

    // Tambahkan animasi CSS
    if (!document.getElementById('calendarAnimations')) {
        const style = document.createElement('style');
        style.id = 'calendarAnimations';
        style.textContent = `
            @keyframes fadeOut {
                from { opacity: 1; transform: translateY(0); }
                to { opacity: 0; transform: translateY(-10px); }
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            /* Style untuk form check switch */
            .form-check-input {
                cursor: pointer;
            }
            
            .form-check-label {
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            #kuotaContainer, #jenisBimbinganContainer {
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }

    // Inisialisasi style label
    function initializeFormLabels() {
        const kuotaLabel = document.getElementById('kuotaLabel');
        const jenisBimbinganLabel = document.getElementById('jenisBimbinganLabel');
        
        if (kuotaLabel) kuotaLabel.style = inactiveStyle;
        if (jenisBimbinganLabel) jenisBimbinganLabel.style = inactiveStyle;
    }

    // Toggle untuk kuota
    document.getElementById('enableKuota')?.addEventListener('change', function() {
        const kuotaContainer = document.getElementById('kuotaContainer');
        const kuotaLabel = document.getElementById('kuotaLabel');
        
        if (kuotaContainer) kuotaContainer.style.display = this.checked ? 'block' : 'none';
        if (kuotaLabel) kuotaLabel.style = this.checked ? activeStyle : inactiveStyle;
        
        // Reset nilai jika dinonaktifkan
        if (!this.checked && document.getElementById('kuotaBimbingan')) {
            document.getElementById('kuotaBimbingan').value = '1';
        }
    });

    // Toggle untuk jenis bimbingan
    // Di event handler checkbox "enableJenisBimbingan"
document.getElementById('enableJenisBimbingan').addEventListener('change', function() {
    document.getElementById('jenisBimbinganContainer').style.display = this.checked ? 'block' : 'none';
    // Reset nilai jika tidak dicentang
    if (!this.checked) {
        document.getElementById('jenisBimbingan').value = '';
    }
});

// Di fungsi saveEvent saat menyiapkan requestData
const enableJenisBimbingan = document.getElementById('enableJenisBimbingan').checked;
const jenisBimbingan = enableJenisBimbingan ? document.getElementById('jenisBimbingan').value : null;

// Log untuk debugging
console.log('Data jenis bimbingan:', { enableJenisBimbingan, jenisBimbingan });

const requestData = {
    // ...data lain
    jenis_bimbingan: jenisBimbingan,
    enableJenisBimbingan: enableJenisBimbingan
};
    
    // Setup tombol Sembunyikan Panduan
    document.getElementById('hideGuide')?.addEventListener('click', function() {
        const guideBox = document.querySelector('.guide-box');
        const showGuideButton = document.getElementById('showGuideButton');
        
        if (!guideBox) return;
        
        // Animasi menyembunyikan
        guideBox.style.animation = 'fadeOut 0.3s ease-out forwards';
        
        setTimeout(() => {
            guideBox.style.display = 'none';
            if (showGuideButton) {
                showGuideButton.style.display = 'block';
            }
        }, 300);
        
        // Simpan preferensi ke localStorage
        localStorage.setItem('calendarGuideHidden', 'true');
    });

    // Jalankan setup
    setupShowGuideButton();
    
    // Tunggu sebentar untuk memastikan semua elemen DOM sudah siap
    setTimeout(handleGuideVisibility, 100);

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long' }
            },
            timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            },
            timeGridDay: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            }
        },
        firstDay: 1,
        locale: 'id',
        buttonIcons: true,
        navLinks: true,
        editable: true,
        dayMaxEvents: 2,
        selectable: true,
        selectMirror: true,
        nowIndicator: true,
        height: 'auto',
    contentHeight: 'auto',
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        slotDuration: '00:30:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '08:00',
            endTime: '18:00',
        },

        eventDidMount: function(info) {
    const eventEl = info.el;
    const event = info.event;
    
    console.log('Event mounted:', event.title, 'Jenis:', event.extendedProps.jenis_bimbingan);
    
    // Untuk external event
    if (event.extendedProps.source === 'google' || event.extendedProps.isExternal) {
        eventEl.classList.add('external-event');
        return;
    }
    
    // Tambahkan kelas berdasarkan jenis bimbingan langsung ke elemen DOM
    const jenisBimbingan = event.extendedProps.jenis_bimbingan;
    if (jenisBimbingan) {
        if (jenisBimbingan === 'skripsi') {
            eventEl.classList.add('fc-event-skripsi');
        } else if (jenisBimbingan === 'kp') {
            eventEl.classList.add('fc-event-kp');
        } else if (jenisBimbingan === 'mbkm') {
            eventEl.classList.add('fc-event-mbkm');
        } else if (jenisBimbingan === 'akademik') {
            eventEl.classList.add('fc-event-krs'); // krs untuk akademik
        } else if (jenisBimbingan === 'konsultasi') {
            eventEl.classList.add('fc-event-konsultasi');
        } else if (jenisBimbingan === 'lainnya') {
            eventEl.classList.add('fc-event-lainnya');
        }
    }
},
        
        dateClick: function(info) {
            const hari = info.date.getDay();
            if (hari === 0 || hari === 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Tersedia',
                    text: 'Tidak dapat membuat jadwal di hari Sabtu atau Minggu',
                    confirmButtonColor: '#1a73e8'
                });
                return;
            }

            selectedDate = info.date;
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        },

        eventClassNames: function(arg) {
    // Debugging - lihat apa data event yang tersedia
    console.log('Event data:', arg.event.title, arg.event.extendedProps);
    
    const jenisBimbingan = arg.event.extendedProps.jenis_bimbingan;
    
    // Event dari Google Calendar atau external event
    if (arg.event.extendedProps.source === 'google' || arg.event.extendedProps.isExternal) {
        return ['external-event'];
    }
    
    // Jika memiliki jenis bimbingan, return kelas yang sesuai
    if (jenisBimbingan) {
        if (jenisBimbingan === 'skripsi') {
            return ['fc-event-skripsi'];
        } else if (jenisBimbingan === 'kp') {
            return ['fc-event-kp'];  
        } else if (jenisBimbingan === 'mbkm') {
            return ['fc-event-mbkm'];
        } else if (jenisBimbingan === 'akademik') {
            return ['fc-event-krs']; // menggunakan krs untuk akademik
        } else if (jenisBimbingan === 'konsultasi') {
            return ['fc-event-konsultasi'];
        } else if (jenisBimbingan === 'lainnya') {
            return ['fc-event-lainnya'];
        }
    }
    // Default jika tidak ada jenis bimbingan
    return [];
},

eventContent: function(arg) {
    // Format waktu dengan format jam:menit
    const startTime = moment(arg.event.start).format('H:mm');
    
    // Ambil judul event asli atau gunakan default
    const eventTitle = arg.event.title || "Jadwal Bim";
    
    // Tetap gunakan dot dengan warna yang sesuai
    let dotColor = '#1a73e8'; // Default blue
    const jenisBimbingan = arg.event.extendedProps.jenis_bimbingan;
    
    if (jenisBimbingan === 'skripsi') {
        dotColor = '#16a34a';
    } else if (jenisBimbingan === 'kp') {
        dotColor = '#dc2626';
    } else if (jenisBimbingan === 'mbkm') {
        dotColor = '#d97706';
    } else if (jenisBimbingan === 'akademik') {
        dotColor = '#0284c7';
    } else if (jenisBimbingan === 'konsultasi') {
        dotColor = '#f4b400';
    } else if (jenisBimbingan === 'lainnya') {
        dotColor = '#9ca3af';
    }
    // Kembalikan konten HTML tanpa menimpa background-color event
    return {
        html: `
            <div class="fc-event-content">
                <div class="fc-event-dot" style="background-color: ${dotColor};"></div>
                <div class="fc-event-time">${startTime}</div>
                <div class="fc-event-title">${eventTitle}</div>
            </div>
        `
    };
},

eventClick: function(info) {
    // Jika event dari Google Calendar, tampilkan detail sederhana
    if (info.event.extendedProps.source === 'google' || info.event.extendedProps.isExternal) {
        Swal.fire({
            title: info.event.title,
            html: `
                <div class="text-center">
                    <p><strong>Waktu:</strong> ${moment(info.event.start).format('HH:mm')} - ${moment(info.event.end).format('HH:mm')}</p>
                    ${info.event.extendedProps.description ? `<p><strong>Deskripsi:</strong> ${info.event.extendedProps.description}</p>` : ''}
                </div>
            `,
            icon: 'info',
            confirmButtonColor: '#1a73e8'
        });
        return;
    }

    // Parse description untuk memisahkan informasi
    const description = info.event.extendedProps.description || '';
    const descriptionLines = description.split('\n').filter(line => line.trim());
    
    // Mendapatkan informasi jenis bimbingan dan kuota
    const jenisBimbingan = info.event.extendedProps.jenis_bimbingan;
    const hasKuotaLimit = info.event.extendedProps.has_kuota_limit;
    const kuota = info.event.extendedProps.kuota;

    // Membuat tampilan yang lebih terstruktur
    const details = descriptionLines.reduce((acc, line) => {
        if (line.startsWith('Status:')) {
            acc.status = line.replace('Status:', '').trim();
        } else if (line.startsWith('Catatan:')) {
            acc.catatan = line.replace('Catatan:', '').trim();
        } else if (line.startsWith('Kapasitas:')) {
            acc.kapasitas = line.replace('Kapasitas:', '').trim();
        }
        return acc;
    }, {});

    // Mapping jenis bimbingan ke label yang lebih readable
    const jenisBimbinganLabels = {
        'skripsi': 'Bimbingan Skripsi',
        'kp': 'Bimbingan KP',
        'akademik': 'Bimbingan Akademik',
        'konsultasi': 'Konsultasi Pribadi',
        'mbkm': 'Bimbingan MBKM',
        'lainnya': 'Lainnya'
    };

    const jenisBimbinganLabel = jenisBimbingan ? (jenisBimbinganLabels[jenisBimbingan] || jenisBimbingan) : 'Tidak ditentukan';

    // Untuk jadwal bimbingan internal, dapatkan status terbaru
    $.ajax({
        url: `/jadwal/${info.event.extendedProps.id || info.event.id}/status`,
        method: 'GET',
        success: function(statusData) {
            // Tentukan warna status berdasarkan data terbaru
            const statusColor = statusData.status === 'penuh' ? '#dc2626' : 
                            statusData.status === 'selesai' ? '#6b7280' : '#16a34a';
            
            // PENTING: Jika status penuh, tampilkan kapasitas sebagai jumlah pendaftar
            const displayedPendaftar = statusData.status === 'penuh' ? statusData.kapasitas : statusData.jumlah_pendaftar;
            
            console.log('Status data:', statusData);
            console.log('displayedPendaftar:', displayedPendaftar);              

            Swal.fire({
                title: '<div style="font-size: 18px; font-weight: 600; color: #1a202c; margin-bottom: 8px;">Detail Jadwal Bimbingan</div>',
                html: `
                    <div class="compact-popup-container" style="text-align: left; padding: 0px;">
                        <!-- Time Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: #eef2ff; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-clock" style="font-size: 16px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Waktu</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${moment(info.event.start).format('HH:mm')} - ${moment(info.event.end).format('HH:mm')}</div>
                            </div>
                        </div>

                        <!-- Type Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: #fef3c7; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-bookmark-star" style="font-size: 16px; color: #d97706;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Jenis Bimbingan</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${jenisBimbinganLabel}</div>
                            </div>
                        </div>

                        <!-- Quota Section if exists -->
                        ${hasKuotaLimit ? `
                            <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                                <div style="background: #ecfdf5; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                    <i class="bi bi-people" style="font-size: 16px; color: #059669;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Kuota</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${displayedPendaftar}/${statusData.kapasitas} Mahasiswa</div>
                                </div>
                            </div>
                        ` : ''}

                        <!-- Status Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: ${statusData.status === 'tersedia' ? '#dcfce7' : statusData.status === 'penuh' ? '#fee2e2' : '#f3f4f6'}; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-info-circle" style="font-size: 16px; color: ${statusColor};"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Status</div>
                                <div style="font-size: 14px; font-weight: 600; color: ${statusColor};">${statusData.status_label}</div>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash" style="margin-right: 6px;"></i>Hapus Jadwal',
                cancelButtonText: '<i class="bi bi-x-lg" style="margin-right: 6px;"></i>Tutup',
                showCloseButton: true,
                width: '420px',
                customClass: {
                    popup: 'compact-swal-popup',
                    title: 'compact-swal-title',
                    confirmButton: 'compact-swal-button',
                    cancelButton: 'compact-swal-button',
                    actions: 'compact-swal-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Konfirmasi penghapusan
                    Swal.fire({
                        title: 'Hapus Jadwal?',
                        text: "Jadwal yang dihapus tidak dapat dikembalikan",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            hapusJadwal(info.event.id);
                        }
                    });
                }
            });
        },
        error: function() {
            // Fallback jika ada error: gunakan data dari event
            const statusColor = info.event.extendedProps.status === 'penuh' ? '#dc2626' : 
                                info.event.extendedProps.status === 'selesai' ? '#6b7280' : '#16a34a';
                                
            const statusLabel = info.event.extendedProps.status === 'penuh' ? 'Penuh' : 
                               info.event.extendedProps.status === 'selesai' ? 'Selesai' : 'Tersedia';
            
            const displayedPendaftar = info.event.extendedProps.status === 'penuh' ? info.event.extendedProps.kuota : (info.event.extendedProps.jumlah_pendaftar || 0);

            Swal.fire({
                // Gunakan format yang sama dengan kode di atas, tetapi dengan data dari event,
                // bukan dari response AJAX
                title: '<div style="font-size: 18px; font-weight: 600; color: #1a202c; margin-bottom: 8px;">Detail Jadwal Bimbingan</div>',
                html: `
                    <div class="compact-popup-container" style="text-align: left; padding: 0px;">
                        <!-- Time Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: #eef2ff; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-clock" style="font-size: 16px; color: #3b82f6;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Waktu</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${moment(info.event.start).format('HH:mm')} - ${moment(info.event.end).format('HH:mm')}</div>
                            </div>
                        </div>

                        <!-- Type Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: #fef3c7; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-bookmark-star" style="font-size: 16px; color: #d97706;"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Jenis Bimbingan</div>
                                <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${jenisBimbinganLabel}</div>
                            </div>
                        </div>

                        <!-- Quota Section if exists -->
                        ${hasKuotaLimit ? `
                            <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                                <div style="background: #ecfdf5; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                    <i class="bi bi-people" style="font-size: 16px; color: #059669;"></i>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Kuota</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #1e293b;">${info.event.extendedProps.jumlah_pendaftar || 0}/${info.event.extendedProps.kuota || 0} Mahasiswa</div>
                                </div>
                            </div>
                        ` : ''}

                        <!-- Status Section -->
                        <div class="info-row" style="display: flex; align-items: center; margin-bottom: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                            <div style="background: ${info.event.extendedProps.status === 'tersedia' ? '#dcfce7' : info.event.extendedProps.status === 'penuh' ? '#fee2e2' : '#f3f4f6'}; padding: 8px; border-radius: 6px; margin-right: 12px;">
                                <i class="bi bi-info-circle" style="font-size: 16px; color: ${statusColor};"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Status</div>
                                <div style="font-size: 14px; font-weight: 600; color: ${statusColor};">${statusLabel}</div>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash" style="margin-right: 6px;"></i>Hapus Jadwal',
                cancelButtonText: '<i class="bi bi-x-lg" style="margin-right: 6px;"></i>Tutup',
                showCloseButton: true,
                width: '420px',
                customClass: {
                    popup: 'compact-swal-popup',
                    title: 'compact-swal-title',
                    confirmButton: 'compact-swal-button',
                    cancelButton: 'compact-swal-button',
                    actions: 'compact-swal-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Konfirmasi penghapusan (sama dengan di atas)
                    Swal.fire({
                        title: 'Hapus Jadwal?',
                        text: "Jadwal yang dihapus tidak dapat dikembalikan",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            hapusJadwal(info.event.id);
                        }
                    });
                }
            });
        }
    });
},

events: function(fetchInfo, successCallback, failureCallback) {
    console.log('Memulai loading events untuk bulan:', fetchInfo.startStr, 'hingga', fetchInfo.endStr);
    
    // Gunakan URL lengkap dengan leading slash
    fetch('/dosen/google/events')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                console.error('Error response:', response.statusText);
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(events => {
            // Debug: cek data yang diterima
            console.log('Events yang diterima:', events);
            events.forEach(event => {
                console.log('Event:', event.title, 'extendedProps:', event.extendedProps);
                if (event.extendedProps?.jenis_bimbingan) {
                    console.log('Jenis Bimbingan ditemukan:', event.extendedProps.jenis_bimbingan);
                }
            });
            successCallback(events);
        })
        .catch(error => {
            console.error('Fetch error:', error);
            failureCallback(error);
            tampilkanPesan('error', 'Gagal memuat jadwal: ' + error.message);
        });
    }
    });

    calendar.render();
    
    // Force resize after render
    console.log('Calendar rendered. Forcing resize in 2 seconds...');
    setTimeout(() => {
        console.log('Resizing calendar...');
        calendar.updateSize();
    }, 2000);

    // Handler Simpan Jadwal
    document.getElementById('saveEvent')?.addEventListener('click', async function() {
    try {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (!startTime || !endTime) {
            throw new Error('Mohon isi waktu mulai dan selesai');
        }

        // Buat objek tanggal dari selectedDate
        const startDateTime = new Date(selectedDate);
        const endDateTime = new Date(selectedDate);
        
        // Parse waktu
        const [startHour, startMinute] = startTime.split(':');
        const [endHour, endMinute] = endTime.split(':');
        
        // Set jam dan menit
        startDateTime.setHours(parseInt(startHour), parseInt(startMinute), 0, 0);
        endDateTime.setHours(parseInt(endHour), parseInt(endMinute), 0, 0);
        
        // Validasi waktu selesai harus setelah waktu mulai
        if (endDateTime <= startDateTime) {
            throw new Error('Waktu selesai harus setelah waktu mulai');
        }

        // Validasi jam kerja (08:00 - 18:00)
        const startHourInt = parseInt(startHour);
        if (startHourInt < 8 || startHourInt >= 18) {
            throw new Error('Jadwal harus dalam jam kerja (08:00 - 18:00)');
        }

        // Hitung durasi dalam menit
        const durationMs = endDateTime.getTime() - startDateTime.getTime();
        const durationMinutes = Math.floor(durationMs / (1000 * 60));

        // Validasi durasi minimum (30 menit)
        if (durationMinutes < 30) {
            throw new Error(`Durasi minimum bimbingan adalah 30 menit. Durasi saat ini: ${durationMinutes} menit`);
        }

        // Dapatkan nilai setting kuota dan jenis bimbingan
        const hasKuotaLimit = document.getElementById('enableKuota').checked;
        const kuota = hasKuotaLimit ? parseInt(document.getElementById('kuotaBimbingan').value) : null;
        
        // Perbaikan: Tentukan nilai jenis_bimbingan berdasarkan status checkbox
        const enableJenisBimbingan = document.getElementById('enableJenisBimbingan').checked;
        let jenisBimbingan = null;
        
        if (enableJenisBimbingan) {
            jenisBimbingan = document.getElementById('jenisBimbingan').value;
            // Pastikan dropdown terpilih jika checkbox diaktifkan
            if (!jenisBimbingan) {
                tampilkanPesan('warning', 'Pilih Jenis Bimbingan', 'Silakan pilih jenis bimbingan jika opsi "Tentukan Jenis Bimbingan" diaktifkan');
                return;
            }
        }
        // Log untuk debugging
        console.log('Data jenis bimbingan yang akan dikirim:', {
            enableJenisBimbingan,
            jenisBimbingan
        });
        
        
        // Ambil nilai lokasi jika ada
        const lokasi = document.getElementById('lokasi') ? document.getElementById('lokasi').value : null;
        
   
        // Tambahkan log debug ini sebelum mengirim request
        console.log('Data yang akan dikirim:', {
            start: startDateTime.toISOString(),
            end: endDateTime.toISOString(),
            has_kuota_limit: hasKuotaLimit,
            kuota: kuota,
            enableJenisBimbingan: enableJenisBimbingan,
            jenis_bimbingan: jenisBimbingan,
            lokasi: lokasi
        });

        // Tampilkan loading
        Swal.fire({
            title: 'Menyimpan Jadwal',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const requestData = {
            start: startDateTime.toISOString(),
            end: endDateTime.toISOString(),
            has_kuota_limit: hasKuotaLimit,
            kuota: kuota,
            jenis_bimbingan: jenisBimbingan,
            lokasi: lokasi,
            enableJenisBimbingan: enableJenisBimbingan // Tambahkan flag ini untuk server
        };

        const response = await fetch('/masukkanjadwal/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        });
        
        console.log('Response status:', response.status);
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('Parsed result:', result);
        } catch (e) {
            console.error('Error parsing response:', e);
            throw new Error('Invalid server response');
        }

        if (!response.ok) {
            throw new Error(result.message || 'Terjadi kesalahan pada server');
        }
        
        if (result.success) {
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('eventModal'));
            if (modalInstance) {
                modalInstance.hide();
            }
            document.getElementById('eventForm').reset();
            calendar.refetchEvents();
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Jadwal berhasil ditambahkan',
                confirmButtonColor: '#1a73e8'
            });
        } else {
            throw new Error(result.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Gagal menambahkan jadwal',
            confirmButtonColor: '#1a73e8'
        });
    }
});
    // Fungsi Hapus Jadwal
    async function hapusJadwal(eventId) {
        try {
            // Tampilkan loading
            Swal.fire({
                title: 'Menghapus Jadwal',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(`/masukkanjadwal/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const responseText = await response.text();
            console.log('Delete response:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Error parsing delete response:', e);
                throw new Error('Invalid server response');
            }
            
            if (result.success) {
                calendar.refetchEvents();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Jadwal berhasil dihapus dari sistem dan Google Calendar',
                    confirmButtonColor: '#1a73e8'
                });
            } else {
                throw new Error(result.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: error.message || 'Gagal menghapus jadwal',
                confirmButtonColor: '#1a73e8'
            });
        }
    }

    // Reset form when modal is shown
    document.getElementById('eventModal')?.addEventListener('show.bs.modal', function () {
        // Reset form fields
        document.getElementById('startTime').value = '';
        document.getElementById('endTime').value = '';
        document.getElementById('eventDescription').value = '';
        document.getElementById('timeValidationFeedback').innerHTML = '';
        
        // Reset toggle switches
        const enableKuota = document.getElementById('enableKuota');
        const enableJenisBimbingan = document.getElementById('enableJenisBimbingan');
        
        if (enableKuota) enableKuota.checked = false;
        if (enableJenisBimbingan) enableJenisBimbingan.checked = false;
        
        // Reset containers
        const kuotaContainer = document.getElementById('kuotaContainer');
        const jenisBimbinganContainer = document.getElementById('jenisBimbinganContainer');
        
        if (kuotaContainer) kuotaContainer.style.display = 'none';
        if (jenisBimbinganContainer) jenisBimbinganContainer.style.display = 'none';
        
        // Reset values in containers
        const kuotaBimbingan = document.getElementById('kuotaBimbingan');
        const jenisBimbingan = document.getElementById('jenisBimbingan');
        
        if (kuotaBimbingan) kuotaBimbingan.value = '1';
        if (jenisBimbingan) jenisBimbingan.value = '';
        
        // Reset labels
        initializeFormLabels();
        
        // Enable save button
        document.getElementById('saveEvent').disabled = false;
    });

    // Add time validation events
    document.getElementById('startTime')?.addEventListener('change', validateTimes);
    document.getElementById('endTime')?.addEventListener('change', validateTimes);

    // Function to validate time inputs
    function validateTimes() {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        const saveButton = document.getElementById('saveEvent');
        const feedbackEl = document.getElementById('timeValidationFeedback');

        // Reset feedback
        feedbackEl.innerHTML = '';
        saveButton.disabled = false;

        if (startTime && endTime) {
            const [startHour, startMinute] = startTime.split(':');
            const [endHour, endMinute] = endTime.split(':');
            
            const start = new Date();
            start.setHours(parseInt(startHour), parseInt(startMinute), 0, 0);
            
            const end = new Date();
            end.setHours(parseInt(endHour), parseInt(endMinute), 0, 0);

            const durationMinutes = Math.floor((end.getTime() - start.getTime()) / (1000 * 60));
            
            let errorMessage = '';
            
            if (end <= start) {
                errorMessage = 'Waktu selesai harus lebih besar dari waktu mulai';
            } else if (durationMinutes < 30) {
                errorMessage = `Durasi minimum bimbingan adalah 30 menit. Durasi saat ini: ${durationMinutes} menit`;
            } else if (parseInt(startHour) < 8 || parseInt(startHour) >= 18 || 
                    parseInt(endHour) < 8 || parseInt(endHour) > 18) {
                errorMessage = 'Jadwal harus dalam jam kerja (08:00 - 18:00)';
            }

            if (errorMessage) {
                feedbackEl.innerHTML = `<div class="text-danger small mt-2">${errorMessage}</div>`;
                saveButton.disabled = true;
            } else {
                feedbackEl.innerHTML = `<div class="text-success small mt-2">Durasi bimbingan: ${durationMinutes} menit</div>`;
                saveButton.disabled = false;
            }
        }
    }

    // Inisialisasi label saat page load
    initializeFormLabels();
});
</script>
@endpush