@extends('layouts.dashboard')

@section('content')

<div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Holiday</h1>

            <form action="{{ route('holidays.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
                @csrf
                <div class="mb-4">
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">
                        Holiday Title
                    </label>
                    <input type="text" id="title" name="title" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                           required>
                </div>

                <div class="mb-4">
                    <label for="date" class="block mb-2 text-sm font-medium text-gray-900">
                        Date
                    </label>
                    <input type="date" id="date" name="date" 
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                           required>
                </div>

                <div class="mb-4">
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-900">
                        Holiday Type
                    </label>
                    <select id="type" name="type" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            required>
                        <option value="public">Public Holiday</option>
                        <option value="optional">Optional Holiday</option>
                        <option value="restricted">Restricted Holiday</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900">
                        Description (Optional)
                    </label>
                    <textarea id="description" name="description" rows="3" 
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('holidays.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Add Holiday
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection