@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">Managers</h1>
            <a href="{{ route('managers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Add New Manager
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative mt-4 mb-6">
            <input type="text" id="rowSearch" onkeyup="filterRows()" placeholder="Search managers..."  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="overflow-x-auto mt-6">
        <table id="employeeTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($managers as $manager)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td class="px-6 py-4">{{ $manager->manager_name }}</td>
                        <td class="px-6 py-4">{{ $manager->manager_email }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <a href="{{ route('managers.edit', $manager->id) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('managers.destroy', $manager->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <script>
        function filterRows() {
            let input = document.getElementById("rowSearch").value.toLowerCase();
            let table = document.getElementById("employeeTable");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {  // Starting at 1 to skip the header row
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let rowText = "";
                
                for (let j = 0; j < cells.length; j++) {
                    rowText += cells[j].textContent.toLowerCase();
                }

                if (rowText.includes(input)) {
                    row.style.display = "";  // Show row
                } else {
                    row.style.display = "none";  // Hide row
                }
            }
        }
    </script>

@endsection
