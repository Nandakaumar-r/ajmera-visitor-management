<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $year = '2025';
        // Fetch current year holidays
        $holidays = Holiday::orderBy('date')->where('date', 'like', '%' . $year . '%')->get();
        return view('holidays.index', compact('holidays', 'year'));
    }

    public function create()
    {
        return view('holidays.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:public,optional,restricted',
            'description' => 'nullable|string'
        ]);

        Holiday::create($validated);

        return redirect()->route('holidays.index')
            ->with('success', 'Holiday created successfully.');
    }
}
