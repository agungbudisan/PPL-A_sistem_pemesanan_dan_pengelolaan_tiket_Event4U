<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Form Section -->
        <div class="w-3/4 flex flex-col justify-center items-center px-10 bg-[#f9f6f6]">
            <h2 class="text-xl font-semibold mb-6 text-[#3a0000]" style="font-family: Constantia, Georgia, serif;">CREATE ACCOUNT</h2>

            <form method="POST" action="{{ route('register') }}" class="w-full max-w-sm">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <x-text-input id="name" class="block w-full bg-gray-300 border-none rounded" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <x-text-input id="email" class="block w-full bg-gray-300 border-none rounded" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-text-input id="password" class="block w-full bg-gray-300 border-none rounded" type="password" name="password" required autocomplete="new-password" placeholder="Password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <x-text-input id="password_confirmation" class="block w-full bg-gray-300 border-none rounded" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Submit -->
                <div class="mb-4">
                    <button type="submit"
                        class="w-full bg-[#7C0A02] hover:bg-red-800 text-white py-2 rounded" style="font-family: Constantia, Georgia, serif;">
                        Sign Up
                    </button>
                </div>

                <!-- Link to login -->
                <p class="text-sm text-center text-gray-700" style="font-family: Constantia, Georgia, serif;">
                    Already have account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline" style="font-family: Constantia, Georgia, serif;">Sign in</a>
                </p>
            </form>
        </div>

        <!-- Right Banner -->
        <div class="w-1/4 relative overflow-hidden flex flex-col justify-center items-center p-8 bg-[#7C0A02] text-white">
            <!-- Text Layer -->
            <div class="z-10 text-center">
                <h1 class="text-6xl font-bold"  style="font-family: Constantia, Georgia, serif; line-height: 1.4;">WELCOME</h1>
                <p class="mt-2 text-4xl italic" style="font-family: 'Rock Salt', cursive; line-height: 1.4; letter-spacing: 2px;">Are You Ready<br>For Fun?</p>
            </div>

            <!-- Camera Icon Positioned Bottom Right -->
            <img src="{{ asset('storage/login-register/camera-icon.png') }}"
                alt="Camera Icon"
                class="absolute bottom-6 right-6 w-[45%] max-w-[180px] sm:max-w-[200px] md:max-w-[220px] z-10" />

            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('storage/login-register/banner-pattern.png') }}"
                    alt="Pattern"
                    class="object-cover w-full h-full opacity-40" />
            </div>
        </div>
    </div>
</x-guest-layout>
