<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Event4U') }} - Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{
    sidebarOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    }
}" :class="{'dark': darkMode}">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
        <!-- Sidebar -->
        <aside
            id="sidebar"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform sm:translate-x-0"
            aria-label="Sidebar">
            <div class="h-full px-3 py-4 overflow-y-auto bg-gray-800 dark:bg-gray-900 border-r border-gray-700 dark:border-gray-800">
                <div class="flex items-center pl-2.5 mb-5">
                    <span class="self-center text-xl font-semibold whitespace-nowrap text-white">Event4U</span>
                    <span class="ml-2 text-xs font-semibold text-gray-400">Admin</span>
                </div>
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-tachometer-alt w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.events.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.events.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-calendar-alt w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kelola Acara</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-tags w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kelola Kategori</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tickets.index', ['event' => 1]) }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.tickets.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-ticket-alt w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kelola Tiket</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-shopping-cart w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kelola Pesanan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payments.index') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.payments.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-credit-card w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kelola Pembayaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.analytics') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.analytics') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-chart-line w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Analitik</span>
                        </a>
                    </li>
                    <li class="border-t border-gray-700 pt-2 mt-4">
                        <a href="{{ route('welcome') }}" class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700">
                            <i class="fas fa-home w-6 h-6 text-gray-400 transition duration-75 group-hover:text-gray-300"></i>
                            <span class="ml-3">Kembali ke Beranda</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Page Content -->
        <div class="sm:ml-64 p-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $header ?? 'Admin Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Dark mode toggle -->
                    <button @click="toggleDarkMode()" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas" :class="darkMode ? 'fa-sun text-yellow-500' : 'fa-moon text-gray-500'"></i>
                    </button>

                    <!-- Toggle sidebar on mobile -->
                    <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden inline-flex items-center p-2 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>

                    <!-- Notifications dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 relative transition-colors duration-200">
                            <i class="fas fa-bell text-gray-600 dark:text-gray-300"></i>
                            <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                        </button>
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-10">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Notifikasi</h3>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <div class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex">
                                        <div class="flex-shrink-0 pt-0.5">
                                            <i class="fas fa-ticket-alt text-indigo-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Pembelian Tiket Baru</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">2 tiket baru dibeli untuk Konser Musik</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">5 menit yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-2 px-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0 pt-0.5">
                                            <i class="fas fa-calendar-check text-green-500"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Pendaftaran Acara Berhasil</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Acara Workshop Fotografi telah dipublikasikan</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">1 jam yang lalu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="py-2 px-4 text-center border-t border-gray-200 dark:border-gray-700">
                                <a href="#" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Lihat Semua Notifikasi</a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300">
                            <img class="w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="user photo">
                        </button>
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 rounded-lg shadow z-10">
                            <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="font-medium truncate">{{ Auth::user()->email }}</div>
                            </div>
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Profil</a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Pengaturan</a>
                                </li>
                            </ul>
                            <div class="py-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <div x-data="{ show: {{ session('success') || session('error') ? 'true' : 'false' }} }">
                @if (session('success'))
                    <div
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-900 dark:text-green-300 flex justify-between items-center"
                        role="alert">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 text-green-500 hover:text-green-700 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-900 dark:text-red-300 flex justify-between items-center"
                        role="alert">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto -mx-1.5 -my-1.5 text-red-500 hover:text-red-700 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
