@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Kategori: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <form
            action="{{ route('admin.categories.update', $category) }}"
            method="POST"
            enctype="multipart/form-data"
            x-data="{
                name: '{{ old('name', $category->name) }}',
                previewImage: '{{ $category->icon ? asset('storage/' . $category->icon) : '' }}',
                description: '{{ old('description', $category->description) }}',

                handleImageUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewImage = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }"
        >
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kategori <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        x-model="name"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon</label>
                    <div class="mt-1 flex items-center">
                        <template x-if="previewImage">
                            <div class="mr-3 relative">
                                <img :src="previewImage" alt="Preview" class="h-16 w-16 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                <button
                                    type="button"
                                    @click="previewImage = ''; document.getElementById('icon').value = '';"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 focus:outline-none"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <input
                            type="file"
                            name="icon"
                            id="icon"
                            @change="handleImageUpload"
                            accept="image/*"
                            class="block text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800"
                        >
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload gambar ikon untuk kategori (format: JPG, PNG, SVG. Maks: 2MB)</p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <textarea
                    name="description"
                    id="description"
                    x-model="description"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 sm:text-sm"
                ></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penggunaan Kategori -->
            @if($category->events->count() > 0)
            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Penggunaan Kategori</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Kategori ini digunakan oleh {{ $category->events->count() }} acara.</p>
                <div class="mt-2">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Acara terkait:</p>
                    <ul class="mt-1 pl-5 list-disc text-sm text-gray-600 dark:text-gray-400">
                        @foreach($category->events->take(5) as $event)
                            <li>{{ $event->title }}</li>
                        @endforeach
                        @if($category->events->count() > 5)
                            <li>... dan {{ $category->events->count() - 5 }} acara lainnya</li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            <!-- Preview Kategori -->
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Preview Kategori</h3>
                <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div x-show="previewImage" class="flex-shrink-0">
                        <img :src="previewImage" alt="Icon kategori" class="h-12 w-12 object-cover rounded-lg">
                    </div>
                    <div x-show="!previewImage" class="flex-shrink-0 bg-gray-200 dark:bg-gray-600 h-12 w-12 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="name || 'Nama Kategori'"></h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="description || 'Deskripsi kategori akan ditampilkan di sini'"></p>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <i class="fas fa-save mr-2"></i> Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
