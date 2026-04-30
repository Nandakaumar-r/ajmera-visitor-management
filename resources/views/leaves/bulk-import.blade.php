<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Import Leaves') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                            {{ session('warning') }}
                            @if ($errors->any())
                                <ul class="list-disc list-inside mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Instructions</h3>
                        <p class="text-gray-600">Please upload a CSV file with the following columns:</p>
                        <ul class="list-disc list-inside mt-2 text-gray-600">
                            <li>Employee Email</li>
                            <li>Leave Type (e.g., casual, sick, earned)</li>
                            <li>From Date (YYYY-MM-DD)</li>
                            <li>To Date (YYYY-MM-DD)</li>
                            <li>Session 1 (full/half)</li>
                            <li>Session 2 (full/half)</li>
                            <li>Reason</li>
                        </ul>
                        <div class="mt-4">
                            <a href="{{ asset('sample-leave-import.csv') }}" class="text-blue-600 hover:text-blue-800">
                                Download Sample CSV
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('leaves.bulk-import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="csv_file" class="block text-sm font-medium text-gray-700">CSV File</label>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @error('csv_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Import Leaves
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
