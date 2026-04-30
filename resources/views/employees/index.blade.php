@extends('layouts.dashboard')

@section('content')
    <div class="container mx-auto py-10">
        <div class="flex justify-start gap-4 items-center">
            <h1 class="text-3xl text-start font-bold">Employees</h1>
            <a href="{{ route('employees.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Add New Employee
            </a>
            <a href="{{ route('employees.import') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Import
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded mt-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Row Search Input -->
        <div class="my-4">
            <input type="text" id="rowSearch" placeholder="Search employees..." 
                   class="px-4 py-2 border rounded w-full" oninput="filterRows()">
        </div>

        <!-- Employee Table -->
        <div class="overflow-x-auto mt-6">
            <table id="employeeTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Designation</th>
                        <th scope="col" class="px-6 py-3">Department</th>
                        <th scope="col" class="px-6 py-3">Date of Joining</th>
                        <th scope="col" class="px-6 py-3">Manager</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $employee)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                             <td class="px-6 py-4">{{ $employee->employee_id }}</td>
                            <td class="px-6 py-4">{{ $employee->employee_name }}</td>
                            <td class="px-6 py-4">{{ $employee->employee_email }}</td>
                            <td class="px-6 py-4">{{ $employee->employee_designation }}</td>
                            <td class="px-6 py-4">{{ $employee->employee_department }}</td>
                            <td class="px-6 py-4">{{ $employee->employee_date_of_joining }}</td>
                            <td class="px-6 py-4">{{ $employee->manager->manager_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 flex space-x-2">
                                <!-- Send welcome email with password -->
                                <button data-employee-id="{{ $employee->employee_id }}" data-email="{{ $employee->employee_email }}" data-url="{{ route('employees.send-welcome-email') }}" class="text-grey hover:underline sendWelcomeEmail">Welcome Email</button>
                                <a href="{{ route('employees.edit', $employee->employee_id) }}" class="text-blue-600 hover:underline">Edit</a>
                                <form action="{{ route('employees.destroy', $employee->employee_id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
             <div class="mt-4">
            {{ $employees->links() }}
        </div>
        </div>
    </div>

    <!-- JavaScript for Row Filtering -->
    <script>
        function filterRows() {
            let input = document.getElementById("rowSearch").value.toLowerCase();
            let table = document.getElementById("employeeTable");
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let row = rows[i];
                let cells = row.getElementsByTagName("td");
                let rowText = "";
                
                for (let j = 0; j < cells.length; j++) {
                    rowText += cells[j].textContent.toLowerCase();
                }

                if (rowText.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            }
        }

        // Attach event listener to the input field
        document.getElementById("rowSearch").addEventListener("input", filterRows);
        
        // Send welcome email with password by class
        const sendWelcomeEmailButtons = document.querySelectorAll('.sendWelcomeEmail');
        sendWelcomeEmailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-employee-id');
                const email = this.getAttribute('data-email');
                const url = this.getAttribute('data-url');
                const button = this;

                if (employeeId && url) {
                    // Disable button and show loading state
                    button.disabled = true;
                    button.textContent = 'Sending...';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ employeeId, email })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Welcome email sent successfully!');
                            button.textContent = 'Email Sent';
                            button.classList.add('text-green-600');
                        } else {
                            throw new Error(data.message || 'Failed to send email');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Failed to send welcome email. Please try again.');
                        button.textContent = 'Retry Send Email';
                        button.classList.add('text-red-600');
                    })
                    .finally(() => {
                        button.disabled = false;
                    });
                } else {
                    alert('Missing required information. Please try again.');
                }
            });
        });
    </script>
@endsection
