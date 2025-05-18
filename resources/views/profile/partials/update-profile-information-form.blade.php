<section x-data="{ editMode: false }">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("View or update your account's profile information.") }}
        </p>
    </header>

    <!-- View Mode -->
    <div x-show="!editMode" class="mt-6 space-y-4">
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>

        <button @click="editMode = true" class="px-4 py-2 bg-[#7B0015] text-white rounded hover:bg-[#a32020]">
            Edit
        </button>
    </div>

    <!-- Edit Mode -->
    <form x-show="editMode" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            <button type="button" @click="editMode = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
        </div>
    </form>
</section>
