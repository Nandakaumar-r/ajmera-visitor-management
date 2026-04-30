@extends('layouts.app')

@section('title', 'Configure Approval Workflow - ' . $vendor->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Configure Approval Workflow
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Set up the approval workflow for {{ $vendor->name }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.vendors.show', $vendor->id) }}" 
               class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Back to Vendor
            </a>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Vendor Information
            </h3>
        </div>
        <div class="px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Vendor Name
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $vendor->name }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Email
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $vendor->email }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Phone
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $vendor->phone }}
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">
                        Status
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $vendor->status === 'active' ? 'bg-green-100 text-green-800' : 
                               ($vendor->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 
                               ($vendor->status === 'blocked' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                            {{ ucfirst($vendor->status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Approval Workflow Configuration
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Assign approvers for each level of the vendor bill approval process.
            </p>
        </div>
        <form action="{{ route('admin.vendors.update-workflow-config', $vendor->id) }}" method="POST" class="px-4 py-5 sm:px-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Initial Approval -->
                <div>
                    <label for="initial_approver_id" class="block text-sm font-medium text-gray-700">
                        Initial Approver
                    </label>
                    <select id="initial_approver_id" name="initial_approver_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an approver</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}" 
                                {{ (isset($vendor->approvalWorkflow) && $vendor->approvalWorkflow->initial_approver_id == $approver->id) ? 'selected' : '' }}>
                                {{ $approver->name }} ({{ $approver->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- HR Approval -->
                <div>
                    <label for="hr_approver_id" class="block text-sm font-medium text-gray-700">
                        HR Approver
                    </label>
                    <select id="hr_approver_id" name="hr_approver_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an approver</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}" 
                                {{ (isset($vendor->approvalWorkflow) && $vendor->approvalWorkflow->hr_approver_id == $approver->id) ? 'selected' : '' }}>
                                {{ $approver->name }} ({{ $approver->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Finance Approval -->
                <div>
                    <label for="finance_approver_id" class="block text-sm font-medium text-gray-700">
                        Finance Approver
                    </label>
                    <select id="finance_approver_id" name="finance_approver_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an approver</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}" 
                                {{ (isset($vendor->approvalWorkflow) && $vendor->approvalWorkflow->finance_approver_id == $approver->id) ? 'selected' : '' }}>
                                {{ $approver->name }} ({{ $approver->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- CFO Approval -->
                <div>
                    <label for="cfo_approver_id" class="block text-sm font-medium text-gray-700">
                        CFO Approver
                    </label>
                    <select id="cfo_approver_id" name="cfo_approver_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an approver</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}" 
                                {{ (isset($vendor->approvalWorkflow) && $vendor->approvalWorkflow->cfo_approver_id == $approver->id) ? 'selected' : '' }}>
                                {{ $approver->name }} ({{ $approver->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Processor -->
                <div>
                    <label for="payment_processor_id" class="block text-sm font-medium text-gray-700">
                        Payment Processor
                    </label>
                    <select id="payment_processor_id" name="payment_processor_id" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select an approver</option>
                        @foreach($approvers as $approver)
                            <option value="{{ $approver->id }}" 
                                {{ (isset($vendor->approvalWorkflow) && $vendor->approvalWorkflow->payment_processor_id == $approver->id) ? 'selected' : '' }}>
                                {{ $approver->name }} ({{ $approver->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Configuration
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
