<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Summary Card -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                        <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-100 border-2 border-[#7B0015]">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=FFFFFF&background=7B0015"
                                 alt="{{ $user->name }}" class="h-full w-full object-cover">
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-[#7B0015] text-white">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 ml-2 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Member sejak {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Ringkasan Aktivitas') }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Pemesanan</p>
                                @php
                                    $totalOrders = \App\Models\Order::where('user_id', $user->id)->count();
                                @endphp
                                <p class="text-xl font-semibold">{{ $totalOrders }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Acara Dihadiri</p>
                                @php
                                    $attendedEvents = \App\Models\Order::where('user_id', $user->id)
                                        ->whereHas('payment', function($query) {
                                            $query->where('status', 'completed');
                                        })
                                        ->whereHas('ticket.event', function($query) {
                                            $query->where('start_event', '<', now());
                                        })
                                        ->count();
                                @endphp
                                <p class="text-xl font-semibold">{{ $attendedEvents }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Acara Mendatang</p>
                                @php
                                    $upcomingEvents = \App\Models\Order::where('user_id', $user->id)
                                        ->whereHas('payment', function($query) {
                                            $query->where('status', 'completed');
                                        })
                                        ->whereHas('ticket.event', function($query) {
                                            $query->where('start_event', '>', now());
                                        })
                                        ->count();
                                @endphp
                                <p class="text-xl font-semibold">{{ $upcomingEvents }}</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-calendar text-purple-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
