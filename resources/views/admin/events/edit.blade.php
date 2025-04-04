@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Acara: {{ $event->title }}</h1>
    <div class="flex space-x-2">
        <a href="{{ route('admin.tickets.index', $event) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-ticket-alt mr-2"></i> Kelola Tiket
        </a>
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <form
            x-data="{
                title: '{{ old('title', $event->title) }}',
                description: '{{ old('description', $event->description) }}',
                location: '{{ old('location', $event->location) }}',
                category_id: '{{ old('category_id', $event->category_id) }}',
                start_event: '{{ old('start_event', $event->start_event->format('Y-m-d\TH:i')) }}',
                end_event: '{{ old('end_event', $event->end_event->format('Y-m-d\TH:i')) }}',
                start_sale: '{{ old('start_sale', $event->start_sale->format('Y-m-d\TH:i')) }}',
                end_sale: '{{ old('end_sale', $event->end_sale->format('Y-m-d\TH:i')) }}',
                hasStageLayout: {{ old('has_stage_layout', isset($event->stage_layout) ? 'true' : 'false') }},
                thumbnailPreview: '{{ asset('storage/' . $event->thumbnail) }}',
                stageLayoutPreview: '{{ isset($event->stage_layout) ? asset('storage/' . $event->stage_layout) : '' }}',

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
            }"
            action="{{ route('admin.events.update', $event) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul Acara -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Acara <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        x-model="title"
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
                        x-model="category_id"
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
                        x-model="location"
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

                <!-- Tanggal Mulai Acara -->
                <div>
                    <label for="start_event" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai Acara <span class="text-red-500">*</span></label>
                    <input
                        type="datetime-local"
                        name="start_event"
                        id="start_event"
                        x-model="start_event"
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
                        x-model="end_event"
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
                        x-model="start_sale"
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
                        x-model="end_sale"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                        required
                    >
                    @error('end_sale')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi <span class="text-red-500">*</span></label>
                <textarea
                    name="description"
                    id="description"
                    x-model="description"
                    rows="6"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                    required
                ></textarea>
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
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                        >
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
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg"
            >
                <label for="stage_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Layout Panggung
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
                    >
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar layout panggung/venue (format: JPG, PNG. Maks: 2MB). Biarkan kosong jika tidak ingin mengubah.</p>
                @error('stage_layout')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Acara -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview Acara</h3>
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
                    <div x-show="thumbnailPreview" class="w-full h-40 bg-gray-200 dark:bg-gray-600">
                        <img :src="thumbnailPreview" alt="Event Thumbnail" class="w-full h-full object-cover">
                    </div>
                    <div x-show="!thumbnailPreview" class="w-full h-40 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <div class="p-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="title || 'Judul Acara'"></h2>
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <span x-text="location || 'Lokasi'"></span>
                        </div>
                        <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <span x-text="start_event ? new Date(start_event).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : 'Tanggal Acara'"></span>
                        </div>
                        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400" x-text="description ? (description.length > 100 ? description.substr(0, 100) + '...' : description) : 'Deskripsi acara akan ditampilkan di sini'"></div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <i class="fas fa-save mr-2"></i> Perbarui Acara
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
