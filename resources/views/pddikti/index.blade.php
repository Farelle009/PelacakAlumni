@extends('layouts.app')

@section('title', 'Verifikasi PDDIKTI')
@section('page-title', 'Verifikasi PDDIKTI')
@section('page-description', 'Cari, verifikasi, dan sinkronisasi data mahasiswa atau alumni langsung dari pangkalan data PDDIKTI.')

@section('content')
<div class="space-y-6">

    {{-- ── Notifikasi Sukses / Error (Otomatis Hilang) ────────────────────── --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-[-10px]"
            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"
        >
            <div class="flex items-center gap-2 font-medium">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0 translate-y-[-10px]"
            class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"
        >
            <div class="flex items-center gap-2 font-medium">
                <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ── Form Pencarian ────────────────────────────────────────────────────── --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <form 
            action="{{ route('pddikti.search') }}" 
            method="POST" 
            x-data="{ isSubmitting: false }" 
            @submit="isSubmitting = true"
            class="grid grid-cols-1 gap-4 lg:grid-cols-5 lg:items-end"
        >
            @csrf

            {{-- Pilihan Alumni dari Database --}}
            <div class="lg:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Pilih Mahasiswa / Alumni</label>
                <select 
                    name="alumni_id" 
                    required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200 bg-white"
                >
                    <option value="" disabled {{ old('alumni_id') ? '' : 'selected' }}>-- Pilih Alumni --</option>
                    @foreach ($alumniList as $alumni)
                        <option value="{{ $alumni->id }}" {{ old('alumni_id') == $alumni->id ? 'selected' : '' }}>
                            {{ $alumni->nim }} - {{ $alumni->nama_lengkap }} ({{ $alumni->program_studi }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Universitas --}}
            <div class="lg:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Universitas</label>
                <input 
                    type="text" 
                    name="universitas" 
                    value="{{ old('universitas', 'Universitas Muhammadiyah Malang') }}"
                    placeholder="Contoh: Universitas Muhammadiyah Malang"
                    required
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200 bg-slate-50"
                >
            </div>

            {{-- Tombol --}}
            <div class="lg:col-span-1">
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    <svg x-show="isSubmitting" style="display: none;" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    
                    <span x-text="isSubmitting ? 'Mencari...' : 'Cari & Verifikasi'">Cari & Verifikasi</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ── Hasil Pencarian (Muncul Jika Berhasil) ─────────────────────────── --}}
    @if(session('alumni_data'))
        @php $data = session('alumni_data'); @endphp
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-900">Hasil Verifikasi PDDIKTI</h3>
                <p class="text-sm text-slate-500">Detail mahasiswa yang ditemukan dan telah tersimpan di database.</p>
            </div>
            
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                    
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $data->nama ?: '-' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">NIM / NPK</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $data->nim ?: '-' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            {{ $data->jenis_kelamin == 'L' ? 'Laki-Laki' : ($data->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Universitas / PT</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $data->nama_pt ?: '-' }} <span class="text-xs text-slate-400">({{ $data->kode_pt }})</span></dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Program Studi</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $data->prodi ?: '-' }} <span class="text-xs text-slate-400">({{ $data->jenjang }})</span></dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Tanggal Masuk</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $data->tanggal_masuk ? \Carbon\Carbon::parse($data->tanggal_masuk)->format('d M Y') : '-' }}</dd>
                    </div>

                    <div class="sm:col-span-3">
                        <dt class="text-sm font-medium text-slate-500">Status Mahasiswa Saat Ini</dt>
                        <dd class="mt-1">
                            @php
                                $statusLabel = strtolower($data->status);
                                $statusClass = 'bg-slate-100 text-slate-700';
                                
                                if (str_contains($statusLabel, 'lulus')) {
                                    $statusClass = 'bg-emerald-50 text-emerald-700';
                                } elseif (str_contains($statusLabel, 'aktif')) {
                                    $statusClass = 'bg-blue-50 text-blue-700';
                                } elseif (str_contains($statusLabel, 'keluar') || str_contains($statusLabel, 'hilang')) {
                                    $statusClass = 'bg-rose-50 text-rose-700';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">
                                {{ $data->status ?: 'Tidak Diketahui' }}
                            </span>
                        </dd>
                    </div>

                </dl>
            </div>
        </div>
    @endif

</div>
@endsection