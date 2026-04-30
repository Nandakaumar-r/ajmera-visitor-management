@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-start gap-4 items-center">
            <h1 class="text-3xl font-bold">Edit Manager</h1>
            <a href="{{ route('managers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Managers List
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('managers.update', $manager->id) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="manager_name" class="block text-lg font-medium text-gray-900">Manager Name</label>
                <input type="text" name="manager_name" id="manager_name" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('manager_name', $manager->manager_name) }}" required>
            </div>

            <div class="mb-4">
                <label for="manager_email" class="block text-lg font-medium text-gray-900">Email</label>
                <input type="email" name="manager_email" id="manager_email" 
                       class="w-full p-3 border border-gray-300 rounded-lg" 
                       value="{{ old('manager_email', $manager->manager_email) }}" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Update Manager
            </button>
        </form>
    </div>
@endsection
