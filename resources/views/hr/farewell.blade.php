@extends('layouts.dashboard')

@section('content')

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Pending Employee Resignations</h1>

    <table class="table-fixed w-full" id="export-table">
        <thead>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Designation</th>
            <th>Action</th>
        </thead>
        <tbody>
            @forelse($resignations as $resignation)
                <tr>
                    <td>{{ $resignation->employee->employee_id }}</td>
                    <td>{{ $resignation->employee->employee_name }}</td>
                    <td>{{ $resignation->employee->employee_email }}</td>
                    <td>{{ $resignation->employee->employee_department }}</td>
                    <td>{{ $resignation->employee->employee_designation }}</td>
                </tr>
            @empty
                <p class="text-center text-gray-500 text-2xl">No Pending Resignations</p>
            @endforelse
        </tbody>
    </table>

</div>


@endsection