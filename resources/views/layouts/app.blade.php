// layouts/app.blade.php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Alumni Tracker') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <div class="min-h-screen lg:flex">
        <!-- Sidebar -->
        <aside class="w-full border-b border-slate-200 bg-white lg:min-h-screen lg:w-64 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="text-lg font-bold text-slate-900">Alumni Tracker</h1>
                    <p class="text-sm text-slate-500">Sistem Pelacakan Alumni</p>
                </div>
            </div>

            <nav class="px-4 pb-6">
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Dashboard
                    </a>

                    <a href="{{ route('alumni.index') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('alumni.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Data Alumni
                    </a>

                    <a href="{{ route('tracking.index') }}"
                       class="flex items-center rounded-xl px-4 py-3 text-sm font-medium transition
                       {{ request()->routeIs('tracking.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                        Tracking Alumni
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Topbar -->
            <header class="border-b border-slate-200 bg-white">
                <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-slate-500">@yield('page-description', 'Kelola data alumni dan hasil pelacakan')</p>
                    </div>

                    <div class="text-sm text-slate-500">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <main class="p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                        {{ session('info') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        <p class="mb-2 font-semibold">Terjadi kesalahan:</p>
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>