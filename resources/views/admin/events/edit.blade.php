@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Acara: {{ $event->title }}</h1>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-ticket-alt mr-2"></i> KELOLA TIKET
        </a>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> KEMBALI
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
    x-data="{
        hasStageLayout: {{ $event->has_stage_layout ? 'true' : 'false' }},
        thumbnailPreview: '{{ $event->thumbnail ? asset('storage/' . $event->thumbnail) : '' }}',
        stageLayoutPreview: '{{ $event->stage_layout ? asset('storage/' . $event->stage_layout) : '' }}',

        handleThumbnailUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.thumbnailPreview = URL.createObjectURL(file);
            }
        },

        handleStageLayoutUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.stageLayoutPreview = URL.createObjectURL(file);
            }
        }
    }">
    <div class="p-6">
        <!-- Alert Success/Error -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 dark:bg-green-800/20 dark:text-green-400" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 dark:bg-red-800/20 dark:text-red-400" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form
            id="eventForm"
            action="{{ route('admin.events.update', $event) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Section: Informasi Dasar -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Dasar
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Judul Acara -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Acara <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title', $event->title) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                            <select
                                name="category_id"
                                id="category_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="location"
                                id="location"
                                value="{{ old('location', $event->location) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('location')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Thumbnail</label>
                            <div class="mt-1 flex items-center">
                                <template x-if="thumbnailPreview">
                                    <div class="mr-3 relative">
                                        <img :src="thumbnailPreview" alt="Preview" class="h-24 w-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                        <button
                                            type="button"
                                            @click="thumbnailPreview = ''; document.getElementById('thumbnail').value = '';"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 focus:outline-none"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                                <input
                                    type="file"
                                    name="thumbnail"
                                    id="thumbnail"
                                    @change="handleThumbnailUpload"
                                    accept="image/*"
                                    class="block text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                                >
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar thumbnail untuk acara (format: JPG, PNG. Maks: 2MB). Biarkan kosong jika tidak ingin mengubah.</p>
                            @error('thumbnail')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Tanggal & Waktu -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-calendar-alt mr-2"></i> Tanggal & Waktu
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tanggal Mulai Acara -->
                        <div>
                            <label for="start_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Acara <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="start_event"
                                id="start_event"
                                value="{{ old('start_event', $event->start_event ? $event->start_event->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('start_event')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai Acara -->
                        <div>
                            <label for="end_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Acara <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="end_event"
                                id="end_event"
                                value="{{ old('end_event', $event->end_event ? $event->end_event->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('end_event')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai Penjualan -->
                        <div>
                            <label for="start_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Penjualan <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="start_sale"
                                id="start_sale"
                                value="{{ old('start_sale', $event->start_sale ? $event->start_sale->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('start_sale')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai Penjualan -->
                        <div>
                            <label for="end_sale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai Penjualan <span class="text-red-500">*</span></label>
                            <input
                                type="datetime-local"
                                name="end_sale"
                                id="end_sale"
                                value="{{ old('end_sale', $event->end_sale ? $event->end_sale->format('Y-m-d\TH:i') : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                                required
                            >
                            @error('end_sale')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Timeline Visual -->
                    <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-600 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Timeline Acara</h3>
                        <div class="relative">
                            <div class="h-1 bg-gray-200 dark:bg-gray-500 absolute w-full top-4"></div>
                            <div class="flex justify-between relative">
                                <div class="text-center">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Penjualan</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->start_sale ? $event->start_sale->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-ticket-alt"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Penjualan</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->end_sale ? $event->end_sale->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Mulai Acara</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->start_event ? $event->start_event->format('d M Y') : '' }}
                                    </p>
                                </div>
                                <div class="text-center">
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <p class="text-xs mt-1 text-gray-600 dark:text-gray-400">Akhir Acara</p>
                                    <p class="text-xs mt-1 font-medium text-gray-800 dark:text-gray-300">
                                        {{ $event->end_event ? $event->end_event->format('d M Y') : '' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informasi Waktu</h3>
                        <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <li>Pastikan tanggal mulai acara lebih awal dari tanggal selesai acara</li>
                            <li>Periode penjualan tiket harus sebelum tanggal mulai acara</li>
                            <li>Pengguna dapat membeli tiket selama periode penjualan yang ditentukan</li>
                        </ul>
                    </div>
                </div>

                <!-- Section: Detail & Layout -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-align-left mr-2"></i> Detail & Layout
                    </h2>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                        <textarea
                            name="description"
                            id="description"
                            rows="6"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                            required
                        >{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stage Layout Option -->
                    <div class="mt-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    x-model="hasStageLayout"
                                    id="has_stage_layout"
                                    name="has_stage_layout"
                                    type="checkbox"
                                    value="1"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                                >
                                <!-- Input hidden untuk nilai false -->
                                <input type="hidden" name="has_stage_layout" value="0">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="has_stage_layout" class="font-medium text-gray-700 dark:text-gray-300">Acara Menggunakan Layout Panggung</label>
                                <p class="text-gray-500 dark:text-gray-400">Centang jika acara memiliki layout panggung atau denah kursi (venue) yang perlu diperlihatkan kepada pembeli tiket</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stage Layout Upload -->
                    <div
                        x-show="hasStageLayout"
                        x-transition
                        class="mt-4 p-4 bg-gray-100 dark:bg-gray-600 rounded-lg"
                    >
                        <label for="stage_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Layout Panggung <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex items-center">
                            <template x-if="stageLayoutPreview">
                                <div class="mr-3 relative">
                                    <img :src="stageLayoutPreview" alt="Stage Layout" class="h-32 w-40 object-contain rounded-lg border border-gray-300 dark:border-gray-600">
                                    <button
                                        type="button"
                                        @click="stageLayoutPreview = ''; document.getElementById('stage_layout').value = '';"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 focus:outline-none"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                            <input
                                type="file"
                                name="stage_layout"
                                id="stage_layout"
                                @change="handleStageLayoutUpload"
                                accept="image/*"
                                class="block text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                                x-bind:required="hasStageLayout"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar layout panggung/venue (format: JPG, PNG. Maks: 2MB)</p>
                        @error('stage_layout')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview Acara -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-eye mr-2"></i> Preview Tampilan Detail Acara
                    </h2>

                    <div class="relative rounded-xl overflow-hidden shadow-md bg-gradient-to-r from-[#7B0015] to-[#AF0020]">
                        <!-- Header section with controlled height -->
                        <div class="flex flex-col md:flex-row items-center">
                            <!-- Thumbnail dengan ukuran terkontrol di sisi kiri (hanya pada desktop) -->
                            <template x-if="thumbnailPreview">
                                <div class="hidden md:block md:w-1/3 h-40 overflow-hidden">
                                    <div class="w-full h-full relative">
                                        <img :src="thumbnailPreview"
                                            alt="{{ $event->title }}"
                                            class="object-contain w-full h-full p-2" />
                                    </div>
                                </div>
                            </template>

                            <!-- Content di sisi kanan (atau penuh pada mobile) -->
                            <div class="p-6 md:p-8" :class="thumbnailPreview ? 'md:w-2/3' : 'w-full'">
                                <div>
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="inline-block bg-white/20 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $event->category->name ?? 'Kategori' }}
                                        </span>

                                        @php
                                            $now = now();
                                            $isUpcoming = $now < $event->start_event;
                                            $isOngoing = $now >= $event->start_event && $now <= $event->end_event;
                                            $isPast = $now > $event->end_event;
                                        @endphp

                                        <span class="inline-block
                                            {{ $isUpcoming ? 'bg-blue-600' : ($isOngoing ? 'bg-green-600' : 'bg-gray-600') }}
                                            text-white text-xs px-2 py-1 rounded">
                                            {{ $isUpcoming ? 'Akan Datang' : ($isOngoing ? 'Sedang Berlangsung' : 'Selesai') }}
                                        </span>
                                    </div>

                                    <h1 class="text-xl md:text-2xl font-bold text-white mb-2">{{ $event->title }}</h1>

                                    <div class="flex flex-wrap text-white/80 text-sm gap-4 mt-4">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-2"></i>
                                            <span>{{ $event->start_event ? $event->start_event->format('d F Y') : 'Tanggal Acara' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-clock mr-2"></i>
                                            <span>{{ $event->start_event ? $event->start_event->format('H:i') : 'Waktu' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <span>{{ $event->location }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile thumbnail yang lebih kecil (hanya tampil di mobile) -->
                        <template x-if="thumbnailPreview">
                            <div class="md:hidden w-full h-40 bg-gray-100 overflow-hidden relative">
                                <img :src="thumbnailPreview"
                                    alt="{{ $event->title }}"
                                    class="object-contain w-full h-full" />
                            </div>
                        </template>
                    </div>

                    <!-- Stage Layout Preview (if enabled) -->
                    <template x-if="hasStageLayout && stageLayoutPreview">
                        <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Layout Venue</h4>
                            <img :src="stageLayoutPreview" alt="Layout Venue" class="w-full h-auto rounded-lg" />
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-8 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <i class="fas fa-save mr-2"></i> Perbarui Acara
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
