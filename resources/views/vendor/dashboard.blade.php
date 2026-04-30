@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Vendor Dashboard</h1>
    <div class="bg-white shadow rounded p-4">
        <p class="mb-2">Welcome, {{ $vendor->name ?? Auth::user()->name }}!</p>
        <p class="text-sm text-gray-600">Status: <span class="font-medium">{{ ucfirst(str_replace('_',' ', $vendor->status)) }}</span></p>
    </div>
</div>
@endsection
