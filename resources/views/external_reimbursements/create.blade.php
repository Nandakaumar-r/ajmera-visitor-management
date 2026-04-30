@extends('layouts.app')

@section('content')
<div class="max-w-2 mx-8 p-6 bg-white rounded-xl shadow-md mt-16">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">External Reimbursement Form</h2>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    {{-- Display Validation Errors --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Flash Error (Optional) --}}
    @if(session('error'))
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <form id="uploadForm" action="{{ route('external.reimbursements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- <h2 class="text-2xl font-bold mb-6 text-left text-gray-800">External Reimbursement</h2> -->

        <!-- Manager Approval & Bills Attachments side by side -->
        <div class="flex gap-10 mb-5">
            <!-- Manager Approval Attachment -->
            <div class="w-1/2">
                <label class="block text-gray-700 font-medium mb-2">Manager Approval Attachment:</label>
                <input type="file" name="manager_approval_attachment" required
                    accept=".jpg,.jpeg,.png, .pdf"
                    class="block w-full text-sm text-gray-700 bg-gray-50 rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Accepted formats: JPG, JPEG, PNG, PDF. Max size: 2MB.</p>
            </div>

            <!-- Bills Attachment -->
            <div class="w-1/2">
                <label class="block text-gray-700 font-medium mb-2 ml-2">Bills Attachment:</label>
                <input type="file" name="bills_attachment[]" multiple required
                    accept=".jpg,.jpeg,.png, .pdf"
                    class="block w-full text-sm text-gray-700 bg-gray-50 rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500 ml-2">
                <p class="text-sm text-gray-500 mt-1 ml-2">Accepted formats: JPG, JPEG, PDF, PNG. Max size: 10MB.</p>
            </div>
        </div>

        <!-- Reimbursement Excel Sheet field -->
        <div class="mb-5">
            <label class="block text-gray-700 font-medium mb-2">Reimbursement Excel Sheet (.xlsx or .xls)</label>
            <input type="file" name="reimbursement_excel" required
                accept=".xls,.xlsx"
                class="block w-50 text-sm text-gray-700 bg-gray-50 rounded border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <!-- Download Sample Excel -->
        <div class="mb-5">
            <a href="{{ asset('Reimbursement Form-Final.xls') }}" download
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                Download Sample Reimbursement Excel Sheet
            </a>
        </div>

        <button type="submit" class="w-40 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
            Submit
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-primary font-semibold py-2 px-4 rounded transition duration-200">
            Cancel
        </a>
    </form>
</div>
@endsection