<div class="h-full bg-white sidebar flex flex-col">
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
        <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
            {{-- <div class="h-8 w-8 rounded-full bg-[#7B0015] flex items-center justify-center">
                <i class="fas fa-ticket-alt text-white text-sm"></i>
            </div> --}}
            <span class="text-lg font-bold text-gray-900">Event4U</span>
        </a>
    </div>

    <div class="p-4 flex-1 overflow-y-auto">
        <div class="flex flex-col space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[#7B0015] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-home w-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Dashboard</span>
            </a>

            <a href="{{ route('orders.index') }}"
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('orders.*') ? 'bg-[#7B0015] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-shopping-cart w-5 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Pesanan Saya</span>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-[#7B0015] text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-user w-5 {{ request()->routeIs('profile.*') ? 'text-white' : 'text-[#7B0015]' }}"></i>
                <span class="ml-3">Profil Saya</span>
            </a>
        </div>

        <div class="pt-4 mt-4 border-t border-gray-200">
            <a href="{{ route('events.index') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-search w-5 text-[#7B0015]"></i>
                <span class="ml-3">Jelajahi Acara</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt w-5 text-[#7B0015]"></i>
                    <span class="ml-3">Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <!-- User info at bottom of sidebar - only shown on desktop -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <div class="h-9 w-9 rounded-full overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=7B0015"
                         alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                    {{ Auth::user()->email }}
                </p>
            </div>
            <a href="{{ route('profile.edit') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
</div>
