<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::latest()->paginate(10);
        return view('forms.index', compact('forms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'form_code' => 'required|string|unique:forms',
            'description' => 'required|string',
            'version' => 'required|string',
            'category' => 'required|in:HR,Finance,Administrative,IT,Other',
            'requires_approval' => 'boolean',
            'file' => 'required|file|max:10240'
        ]);

        $filePath = $request->file('file')->store('forms');

        Form::create([
            ...$validated,
            'file_path' => $filePath,
            'status' => 'active'
        ]);

        return redirect()->route('forms.index')
            ->with('success', 'Form created successfully');
    }
}
