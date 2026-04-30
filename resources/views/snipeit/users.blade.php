@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">All Users</h1>

    <!-- Search Input -->
    <input type="text" id="search" placeholder="Search users..." class="mb-4 p-2 border border-gray-300 rounded w-full">

    <div id="users-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($users['rows'] as $user)
        <div class="user-card bg-white shadow-md rounded-lg p-4 border border-gray-200" data-name="{{ strtolower($user['name']) }}">
            <div class="flex items-center mb-4">
                <img src="{{ $user['avatar'] }}" alt="" class="w-16 h-16 rounded-full mr-4">
                <div>
                    <h2 class="text-xl font-semibold">{{ $user['name'] }}</h2>
                    <p class="text-gray-600">{{ $user['email'] }}</p>
                </div>
            </div>
            <p class="text-gray-500"><strong>ID:</strong> {{ $user['id'] }}</p>
            <p class="text-gray-500"><strong>Created At:</strong> {{ \Carbon\Carbon::parse($user['created_at']['formatted'])->format('Y-m-d') }}</p>
            <p class="text-gray-500"><strong>Assets:</strong> {{ $user['assets_count'] }}</p>
            <p class="text-gray-500"><strong>Licenses:</strong> {{ $user['licenses_count'] }}</p>
            <p class="text-gray-500"><strong>Accessories:</strong> {{ $user['accessories_count'] }}</p>
            <p class="text-gray-500"><strong>Consumables:</strong> {{ $user['consumables_count'] }}</p>
            <div class="flex justify-end">
                <a href="{{ route('snipeit.user.show.single', $user['id']) }}" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-full hover:bg-blue-600 transition duration-200 float-end">View Details</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const users = document.querySelectorAll('.user-card');

        users.forEach(user => {
            const userName = user.getAttribute('data-name');
            if (userName.includes(searchValue)) {
                user.style.display = '';
            } else {
                user.style.display = 'none';
            }
        });
    });
</script>
@endsection
