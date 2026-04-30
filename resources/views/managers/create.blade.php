<!-- Managers create form -->

@extends('layouts.dashboard')
@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-start gap-4 items-center">
            <h1 class="text-3xl font-bold">Add New Manager</h1>
            <a href="{{ route('managers.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Managers List
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif   

        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded mt-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('managers.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded-md p-2">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" class="w-full border rounded-md p-2">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded-md p-2">
            </div>            
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 font-bold mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded-md p-2">
            </div>            
            <div class="mb-4">
                <label for="manager_name" class="block text-gray-700 font-bold mb-2">Manager Name</label>
                <input type="text" name="manager_name" id="manager_name" class="w-full border rounded-md p-2">
            </div>
            <div class="mb-4">
                <label for="manager_email" class="block text-gray-700 font-bold mb-2">Manager Email</label>
                <input type="email" name="manager_email" id="manager_email" class="w-full border rounded-md p-2">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Manager</button>        
        </form>
    </div>
@endsection