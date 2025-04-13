<x-guest-layout>
    <div class="flex min-h-screen">
        <!-- Left Side (Login Form) -->
        <div class="w-full md:w-3/4 flex items-center justify-center bg-[#fdf7f7]">
            <div class="w-full max-w-md p-8 space-y-6">
                <h1 class="text-2xl font-semibold text-center text-[#510101] tracking-wide" style="font-family: Constantia, Georgia, serif;">LOGIN ACCOUNT</h1>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            autofocus 
                            :value="old('email')" 
                            placeholder="Email"
                            class="block w-full px-4 py-2 bg-gray-300 rounded-md focus:outline-none placeholder-gray-600 text-sm"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            required 
                            placeholder="Password"
                            class="block w-full px-4 py-2 bg-gray-300 rounded-md focus:outline-none placeholder-gray-600 text-sm"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me + Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="mr-2 text-[#510101]" style="font-family: Constantia, Georgia, serif;">
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#510101] hover:underline" href="{{ route('password.request') }}" style="font-family: Constantia, Georgia, serif;">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <div class="flex flex-col items-center mt-4">
                        <button type="submit" class="w-full bg-[#7f0000] hover:bg-[#5e0000] text-white py-2 rounded-full" style="font-family: Constantia, Georgia, serif;">
                            Sign In
                        </button>
                        <p class="mt-4 text-sm text-gray-700" style="font-family: Constantia, Georgia, serif;">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-blue-600 hover:underline" style="font-family: Constantia, Georgia, serif;">Register</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side (Image) -->
        <div class="hidden md:block md:w-1/4 relative">
            <img src="{{ asset('storage/login-register/moon-banner.png') }}" alt="Login Image" class="object-cover w-full h-full">
        </div>
    </div>
</x-guest-layout>
