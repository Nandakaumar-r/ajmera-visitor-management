@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-start gap-4 items-center">
            <h1 class="text-3xl text-start font-bold">Designations</h1>
            <a href="{{ route('designations.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Add New Designation
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Row Search Input -->
        <div class="my-4">
            <input type="text" id="rowSearch" placeholder="Search designations..." 
                   class="px-4 py-2 border rounded w-full" oninput="filterRows()">
        </div>

        <!-- Designation Table -->
        <div class="overflow-x-auto mt-6">
            <table id="designationTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($designations as $designation)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $designation->designation_name }}</td>
                            <td class="px-6 py-4">{{ $designation->description }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('designations.edit', $designation) }}" 
                                       class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <form action="{{ route('designations.destroy', $designation) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this designation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
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
            let table = document.getElementById("designationTable");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let visible = false;
                let cells = rows[i].getElementsByTagName("td");
                
                for (let j = 0; j < cells.length; j++) {
                    let cell = cells[j];
                    if (cell) {
                        let text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(input) > -1) {
                            visible = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = visible ? "" : "none";
            }
        }
    </script>
@endsection
