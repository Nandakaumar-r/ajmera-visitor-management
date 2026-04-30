@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-12xl mx-auto">
        <!-- Breadcrumbs -->
        <nav class="text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li>
                    <a href="{{ route('admin.vendors.index') }}" class="text-gray-600 hover:text-gray-900">Vendors</a>
                </li>
                <li><span class="px-1 text-gray-400">/</span></li>
                <li class="text-gray-700">{{ $vendor->name }}</li>
            </ol>
        </nav>
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $vendor->name }}
                    <form method="POST" action="{{ route('admin.vendors.update-status', $vendor->id) }}">
                        @csrf
                        @method('PUT')

                        <select name="status" class="border rounded px-2 py-1">
                            @foreach(['pending_verification', 'active', 'blocked'] as $status)
                            <option value="{{ $status }}" @if($vendor->status === $status) selected @endif>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                            @endforeach
                        </select>

                        <!-- <input type="text" name="comments" placeholder="Comments (optional)" class="border rounded px-2 py-1 mt-2"> -->

                        <button type="submit" class="bg-blue-600 text-white text-sm px-4 py-2 rounded ">Update</button>
                    </form>


                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('admin.vendors.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Back to Vendors
                </a>
            </div>
        </div>
        <!-- Sticky Tabs -->
        <div id="vendor-tabs" class="sticky top-0 z-30 bg-white border-b border-gray-200">
            <div class="flex items-center justify-between">
                <nav class="flex justify-between w-full" aria-label="Tabs">
                    <a href="#overview" data-scroll class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 border-b-2 border-transparent">Overview</a>
                    <a href="#documents" data-scroll class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 border-b-2 border-transparent">Documents</a>
                    <a href="#bank" data-scroll class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 border-b-2 border-transparent">Bank</a>
                    <a href="#bills" data-scroll class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 border-b-2 border-transparent">Bills</a>
                    <a href="#status" data-scroll class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-gray-50 border-b-2 border-transparent">Status</a>
                </nav>
                <div class="hidden sm:flex items-center space-x-2 pr-2">
                    @can('edit-vendor')
                    <a href="{{ route('admin.vendors.edit', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Edit Vendor
                    </a>
                    @endcan
                    @can('manage-vendors')
                    <a href="{{ route('admin.vendors.workflow-config', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Configure Workflow
                    </a>
                    @endcan
                    @can('create-bill')
                    <a href="{{ route('admin.vendors.bills.create', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Add Bill
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="mt-6">
            <div class="">
                <!-- Left Column -->
                <div class="lg:col-span-8">
                    <div id="overview" class="w-full">
                        <!-- Basic Information Card -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Basic Information</h3>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                                <dl class="sm:divide-y sm:divide-gray-200">
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Vendor ID</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->id }}</dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Vendor Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->name }}</dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $vendor->type ? ucfirst($vendor->type) : '—' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->contact_person }}</dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <a href="mailto:{{ $vendor->email }}" class="text-indigo-600 hover:text-indigo-900">{{ $vendor->email }}</a>
                                        </dd>
                                    </div>
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            <a href="tel:{{ $vendor->phone }}" class="text-indigo-600 hover:text-indigo-900">{{ $vendor->phone }}</a>
                                        </dd>
                                    </div>
                                    @if($vendor->nature_of_work)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Nature of Work</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->nature_of_work }}</dd>
                                    </div>
                                    @endif
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Registered On</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->created_at->format('d M Y, h:i A') }}</dd>
                                    </div>
                                    @if($vendor->gst_number)
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">GST Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->gst_number }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>

                        <!-- Address Card -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Address</h3>
                                @can('edit-vendor')
                                <a href="{{ route('admin.vendors.edit', $vendor) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    Edit Address
                                </a>
                                @endcan
                            </div>
                            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                                @if(!empty($vendor->address) || !empty($vendor->city) || !empty($vendor->state) || !empty($vendor->pincode))
                                <dl class="sm:divide-y sm:divide-gray-200">
                                    @if(!empty($vendor->address))
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 whitespace-pre-line">{{ $vendor->address }}</dd>
                                    </div>
                                    @endif
                                    @if(!empty($vendor->city))
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">City</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->city }}</dd>
                                    </div>
                                    @endif
                                    @if(!empty($vendor->state))
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">State</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->state }}</dd>
                                    </div>
                                    @endif
                                    @if(!empty($vendor->pincode))
                                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Pincode</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $vendor->pincode }}</dd>
                                    </div>
                                    @endif
                                </dl>
                                @else
                                <div class="px-6 py-4">
                                    <p class="text-sm text-gray-500">No address information available.</p>
                                    @can('edit-vendor')
                                    <a href="{{ route('admin.vendors.edit', $vendor) }}" class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Add Address
                                    </a>
                                    @endcan
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div id="documents" class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Documents</h3>
                            <a href="{{ route('admin.vendors.documents.index', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z" />
                                    <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                                </svg>
                                Manage Documents
                            </a>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            @if($vendor->type == 'company')
                            <div class="rounded-md bg-blue-50 p-4 mb-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">Company vendors are required to upload a verified GST certificate.</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Required Documents</h4>
                                    <p class="text-xs text-gray-500">The following documents are required for this vendor</p>
                                </div>
                                @can('create-document')
                                <a href="{{ route('admin.vendors.documents.create', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Upload Document
                                </a>
                                @endcan
                            </div>

                            @if(count($vendor->documents) > 0)
                            <div class="flex flex-col">
                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded On</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                                                        <th scope="col" class="relative px-6 py-3">
                                                            <span class="sr-only">Actions</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($vendor->documents->take(5) as $document)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $document->document_type)) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $document->created_at->format('d M Y') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if($document->verified == 0)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                Pending
                                                            </span>
                                                            @elseif($document->verified == 1)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Verified
                                                            </span>
                                                            @elseif($document->verified == 'rejected')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                Rejected
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <a href="{{ str_replace('http://localhost:8000/', 'http://localhost:8000', Storage::url($document->file_path)) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900" title="View">
                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                                </svg>
                                                            </a>
                                                            @can('edit-document')
                                                            <a href="{{ route('admin.vendors.documents.edit', [$vendor, $document]) }}" class="ml-2 text-indigo-600 hover:text-indigo-900" title="Edit">
                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                                </svg>
                                                            </a>
                                                            @endcan
                                                            @can('delete-document')
                                                            <form action="{{ route('admin.vendors.documents.destroy', [$vendor, $document]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="ml-2 text-red-600 hover:text-red-900" title="Delete">
                                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(count($vendor->documents) > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.vendors.documents.index', $vendor) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    View All Documents
                                    <svg class="ml-1 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                            @endif
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> No documents uploaded yet.
                                <a href="{{ route('admin.vendors.documents.create', $vendor) }}" class="alert-link">Upload required documents</a> to complete vendor onboarding.
                            </div>
                            @endif
                        </div>
                    </div>

                    <div id="bank"></div>
                    @if(count($vendor->bankDetails) > 0)
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bank Details</h3>
                            <a href="{{ route('admin.vendors.bank-details.create', $vendor) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Bank Account
                            </a>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex flex-col">
                                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Name</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Number</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IFSC Code</th>
                                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                        <th scope="col" class="relative px-6 py-3">
                                                            <span class="sr-only">Actions</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach($vendor->bankDetails as $bankDetail)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            {{ $bankDetail->bank_name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $bankDetail->account_holder_name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $bankDetail->account_number }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $bankDetail->ifsc_code }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if($bankDetail->is_primary)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Primary
                                                            </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <div class="flex justify-end space-x-2">
                                                                <a href="{{ route('admin.vendors.bank-details.edit', [$vendor, $bankDetail]) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                                    </svg>
                                                                </a>
                                                                <form action="{{ route('admin.vendors.bank-details.destroy', [$vendor, $bankDetail]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this bank account?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                        <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bank Details</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No bank details</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by adding a bank account.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.vendors.bank-details.create', $vendor) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Add Bank Account
                                    </a>
                                </div>
                                @endif

                                <!-- Recent Bills Card -->
                                <div id="bills" class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Bills</h3>
                                        @can('create-bill')
                                        <a href="{{ route('admin.vendors.bills.create', $vendor) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                            </svg>
                                            Add Bill
                                        </a>
                                        @endcan
                                    </div>
                                    <div class="px-4 py-5 sm:p-6">
                                        <div class="flex flex-col">
                                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-50">
                                                                <tr>
                                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bill Number</th>
                                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Amount</th>
                                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GST Amount</th>
                                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                                    <th scope="col" class="relative px-6 py-3">
                                                                        <span class="sr-only">Actions</span>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach($vendor->bills->take(5) as $bill)
                                                                <tr class="hover:bg-gray-50">
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        {{ $bill->bill_number }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ number_format($bill->amount, 2) }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ number_format($bill->tax_amount, 2) }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ $bill->due_date->format('d M Y') }}
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                        @if($bill->status == 'uploaded')
                                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                            Pending
                                                                        </span>
                                                                        @elseif($bill->status == 'transferred')
                                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                            Paid
                                                                        </span>
                                                                        @elseif($bill->status == 'overdue')
                                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                            Overdue
                                                                        </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                                        <div class="flex justify-end space-x-2">
                                                                            <a href="{{ route('admin.bills.show', $bill->id) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                                                </svg>
                                                                            </a>
                                                                            @if($bill->status == 'pending')
                                                                            <a href="{{ route('admin.vendors.bills.pay', [$vendor, $bill]) }}" class="text-green-600 hover:text-green-900" title="Pay">
                                                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                                    <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 011.414 1.414l3-3a1 1 0 011.414-1.414l-3-3a1 1 0 01-1.414 1.414L14.586 9H7V13h7.586l-1.293-1.293z" clip-rule="evenodd" />
                                                                                </svg>
                                                                            </a>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(count($vendor->bills) > 5)
                                            <div class="text-center mt-3">
                                                <a href="{{ route('admin.vendors.bills.index', $vendor) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    View All Bills
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="lg:col-span-4 ">
                            <div class="space-y-6">
                                <!-- Status Card -->
                                <div id="status" class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                                    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">Status</h3>
                                    </div>
                                    <div class="px-4 py-5 sm:p-6">
                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500">Vendor Status:</h4>
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($vendor->status == 'active') bg-green-100 text-green-800
                                            @elseif($vendor->status == 'inactive') bg-yellow-100 text-yellow-800
                                            @elseif($vendor->status == 'blocked') bg-red-100 text-red-800
                                            @endif">
                                                    {{ ucfirst($vendor->status) }}
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-gray-500">Onboarding Status:</h6>
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($vendor->onboarding_status == 'pending_documents') bg-yellow-100 text-yellow-800
                                            @elseif($vendor->onboarding_status == 'documents_uploaded') bg-blue-100 text-blue-800
                                            @elseif($vendor->onboarding_status == 'documents_verified') bg-green-100 text-green-800
                                            @elseif($vendor->onboarding_status == 'approval_pending') bg-indigo-100 text-indigo-800
                                            @elseif($vendor->onboarding_status == 'approved') bg-green-100 text-green-800
                                            @elseif($vendor->onboarding_status == 'rejected') bg-red-100 text-red-800
                                            @endif">
                                                    {{ str_replace('_', ' ', ucfirst($vendor->onboarding_status)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="text-sm font-medium text-gray-500">Verification Status:</h6>
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($vendor->status == 'active') bg-green-100 text-green-800
                                            @else bg-yellow-100 text-yellow-800
                                            @endif">
                                                    {{ $vendor->status == 'active' ? 'Verified' : 'Pending Verification' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-gray-500">Last Updated:</h6>
                                                <p class="text-sm text-gray-500">{{ $vendor->updated_at->format('d M Y, h:i A') }}</p>
                                            </div>
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-gray-500">Registered On:</h6>
                                                <p class="text-sm text-gray-500">{{ $vendor->created_at->format('d M Y, h:i A') }}</p>
                                            </div>
                                            <div>
                                                <h6 class="text-sm font-medium text-gray-500">Last Login:</h6>
                                                <p class="text-sm text-gray-500">{{ $vendor->last_login_at ? $vendor->last_login_at->format('d M Y, h:i A') : 'Never' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @can('update vendor status')
                                    <div class="mt-6 space-x-3">
                                        <form action="{{ route('admin.vendors.update.status', $vendor) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="{{ $vendor->status == 'active' ? 'inactive' : 'active' }}">
                                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white {{ $vendor->status == 'active' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $vendor->status == 'active' ? 'Mark Inactive' : 'Activate' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Edit Vendor
                                        </a>
                                    </div>
                                    @endcan
                                </div>
                            </div>

                            <!-- Summary Card -->
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Summary</h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Bills</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $vendor->bills_count ?? 0 }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Amount</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-gray-900">₹{{ number_format($vendor->bills->sum('amount'), 2) }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Documents</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $vendor->documents_count ?? 0 }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Bank Accounts</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $vendor->bankDetails->count() }}</dd>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bill Status Card -->
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Bill Status</h3>
                                </div>
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Bills</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-yellow-600">{{ $vendor->bills->whereIn('status', ['uploaded', 'under_review'])->count() }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Approved Bills</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $vendor->bills->whereIn('status', ['hr_approved', 'cfo_approved'])->count() }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Paid Bills</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-blue-600">{{ $vendor->bills->where('status', 'transferred')->count() }}</dd>
                                        </div>
                                        <div class="p-4 border border-gray-100 rounded-lg bg-gray-50">
                                            <dt class="text-sm font-medium text-gray-500 truncate">Rejected Bills</dt>
                                            <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $vendor->bills->where('status', 'rejected')->count() }}</dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        (function() {
            const tabs = document.querySelectorAll('#vendor-tabs [data-scroll]');

            function scrollToId(id) {
                const el = document.getElementById(id);
                if (!el) return;
                const tabsEl = document.getElementById('vendor-tabs');
                const offset = (tabsEl ? tabsEl.offsetHeight : 0) + 12;
                const y = el.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });
            }
            tabs.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href') || '';
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        scrollToId(href.substring(1));
                    }
                });
            });
        })();
    </script>
    @endsection