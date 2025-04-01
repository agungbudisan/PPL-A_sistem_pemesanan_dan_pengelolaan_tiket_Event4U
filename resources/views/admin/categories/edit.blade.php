@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold text-gray-900">Edit Kategori: {{ $category->name }}</h1>
    <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-6">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Kategori -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Font Awesome) <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex">
                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                            fa-
                        </span>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $category->icon) }}" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="calendar-alt" required>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Masukkan nama ikon dari Font Awesome tanpa prefix fa-. Contoh: calendar-alt, ticket-alt, music</p>
                    @error('icon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon Preview -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Preview Ikon</h3>
                <div class="flex items-center">
                    <div class="bg-indigo-100 rounded-full p-3 mr-3">
                        <i class="fas fa-{{ $category->icon }} text-indigo-600" id="iconPreview"></i>
                    </div>
                    <span class="text-sm text-gray-600" id="iconText">fa-{{ $category->icon }}</span>
                </div>
            </div>

            <!-- Penggunaan Kategori -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Penggunaan Kategori</h3>
                <p class="text-sm text-gray-600">Kategori ini digunakan oleh {{ $category->events->count() }} acara.</p>
                @if($category->events->count() > 0)
                <div class="mt-2">
                    <p class="text-sm font-medium text-gray-700">Acara terkait:</p>
                    <ul class="mt-1 pl-5 list-disc text-sm text-gray-600">
                        @foreach($category->events->take(5) as $event)
                            <li>{{ $event->title }}</li>
                        @endforeach
                        @if($category->events->count() > 5)
                            <li>... dan {{ $category->events->count() - 5 }} acara lainnya</li>
                        @endif
                    </ul>
                </div>
                @endif
            </div>

            <!-- Tombol Submit -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('iconPreview');
        const iconText = document.getElementById('iconText');

        iconInput.addEventListener('input', function(e) {
            const iconValue = e.target.value.trim();
            iconPreview.className = 'fas fa-' + (iconValue || 'question-circle') + ' text-indigo-600';
            iconText.textContent = 'fa-' + (iconValue || 'question-circle');
        });
    });
</script>
@endpush
@endsection
