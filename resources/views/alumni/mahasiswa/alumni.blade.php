@extends('layouts.app')

@section('title', 'Alumni')

@section('content')

@php
    $activeTab = request()->get('tab', 'form-alumni'); // default tab
@endphp

<style>
    .bg-card-gradient {
        background: linear-gradient(to right,#3875B6, #37C3F4);
    }
</style>

<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Alumni</h1>

    <hr>

    @if ($user->status_alumni == 'aktif' && !$isAllStepsComplete)
        <div class="alert d-flex gap-3 align-items-center border border-danger border-1 bg-danger bg-opacity-10 text-danger fw-bold p-3 rounded" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.707c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            Form Alumni wajib diisi jika kamu sudah menjadi alumni
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white p-0">
            <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                @if (!$isAllStepsComplete)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link px-4 py-3 {{ $activeTab === 'form-alumni' ? 'active' : '' }}"
                        href="{{ request()->url() }}?tab=form-alumni" role="tab">
                        Form Alumni
                        </a>
                    </li>
                @endif
                <li class="nav-item" role="presentation">
                    <a class="nav-link px-4 py-3 {{ $activeTab === 'profil' ? 'active' : '' }}"
                       href="{{ request()->url() }}?tab=profil" role="tab">
                       Profil
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link px-4 py-3 {{ $activeTab === 'performa-alumni' ? 'active' : '' }}"
                       href="{{ request()->url() }}?tab=performa-alumni" role="tab">
                       Performa Alumni
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link px-4 py-3 {{ $activeTab === 'data-alumni' ? 'active' : '' }}"
                       href="{{ request()->url() }}?tab=data-alumni" role="tab">
                       Data Alumni
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link px-4 py-3 {{ $activeTab === 'sebaran-alumni' ? 'active' : '' }}"
                       href="{{ request()->url() }}?tab=sebaran-alumni" role="tab">
                       Sebaran Alumni
                    </a>
                </li>
            </ul>            
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="bimbinganTabContent">
                @if ($activeTab === 'form-alumni')
                    @if (!$isAllStepsComplete)
                        <div class="tab-pane fade show active" id="form" role="tabpanel">
                            @include('alumni.mahasiswa.form-alumni')
                        </div>
                    @endif
                @elseif ($activeTab === 'profil')
                    <div class="tab-pane fade show active" id="data" role="tabpanel">
                        @include('alumni.mahasiswa.profil')
                    </div>
                @elseif ($activeTab === 'performa-alumni')
                    <div class="tab-pane fade show active" id="data" role="tabpanel">
                        @include('alumni.mahasiswa.performa-alumni')
                    </div>
                @elseif ($activeTab === 'data-alumni')
                    <div class="tab-pane fade show active" id="data" role="tabpanel">
                        @include('alumni.mahasiswa.data-alumni')
                    </div>
                @elseif ($activeTab === 'sebaran-alumni')
                    <div class="tab-pane fade show active" id="sebaran" role="tabpanel">
                        @include('alumni.mahasiswa.sebaran-alumni')
                    </div>
                @endif
            </div>            
        </div>
    </div>
</div>
@endsection
