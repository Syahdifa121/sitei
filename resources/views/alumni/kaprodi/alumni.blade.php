@extends('layouts.app')

@section('title', 'Alumni')

@section('content')

@php
    $activeTab = request()->get('tab', 'statistik-alumni'); // default tab
@endphp

<style>
    .bg-card-gradient {
        background: linear-gradient(to right,#3875B6, #37C3F4);
    }
</style>

<div class="container mt-5">
    <h1 class="mb-2 gradient-text fw-bold">Alumni</h1>

    <hr>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white p-0">
            <ul class="nav nav-tabs" id="bimbinganTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link px-4 py-3 {{ $activeTab === 'statistik-alumni' ? 'active' : '' }}"
                       href="{{ request()->url() }}?tab=statistik-alumni" role="tab">
                       Statistik Alumni
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
                @if ($activeTab === 'statistik-alumni')
                    <div class="tab-pane fade show active" id="data" role="tabpanel">
                        @include('alumni.kaprodi.statistik-alumni')
                    </div>
                @elseif ($activeTab === 'data-alumni')
                    <div class="tab-pane fade show active" id="data" role="tabpanel">
                        @include('alumni.kaprodi.data-alumni')
                    </div>
                @elseif ($activeTab === 'sebaran-alumni')
                    <div class="tab-pane fade show active" id="sebaran" role="tabpanel">
                        {{-- @include('alumni.mahasiswa.sebaran-alumni') --}}
                    </div>
                @endif
            </div>            
        </div>
    </div>
</div>
@endsection
