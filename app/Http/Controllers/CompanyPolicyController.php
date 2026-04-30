<?php

namespace App\Http\Controllers;

use App\Models\CompanyPolicy;
use Illuminate\Http\Request;

class CompanyPolicyController extends Controller
{
    public function index()
    {
        $policies = CompanyPolicy::with(['creator', 'approver'])
            ->latest()
            ->paginate(10);

        return view('policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'policy_code' => 'required|string|unique:company_policies',
            'version' => 'required|string',
            'effective_date' => 'required|date',
            'review_date' => 'required|date|after:effective_date',
            'file' => 'required|file|max:10240'
        ]);

        $filePath = $request->file('file')->store('policies');

        CompanyPolicy::create([
            ...$validated,
            'file_path' => $filePath,
            'created_by' => auth()->id(),
            'status' => 'draft'
        ]);

        return redirect()->route('policies.index')
            ->with('success', 'Policy created successfully');
    }
}
