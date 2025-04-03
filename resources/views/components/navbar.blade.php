<!-- resources/views/components/navbar.blade.php -->
<nav class="bg-[#7B0015] text-white py-6">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-[#F7ECAC] text-2xl font-bold">Event 4 U</h1>
        <ul class="flex space-x-6">
            <li><a href="{{ url('/') }}" class="text-[#F7ECAC] hover:text-white">Home</a></li>
            <li><a href="#" class="text-[#F7ECAC] hover:text-white">Category</a></li>
            <li><a href="#" class="text-[#F7ECAC] hover:text-white">Contact</a></li>

            @auth
                <li><a href="{{ route('dashboard') }}" class="hover:text-gray-300">Dashboard</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-gray-300">Logout</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="text-white hover:text-gray-300">Login</a></li>
                <li><a href="{{ route('register') }}" class="text-white hover:text-gray-300">Register</a></li>
            @endauth
        </ul>
    </div>
</nav>
