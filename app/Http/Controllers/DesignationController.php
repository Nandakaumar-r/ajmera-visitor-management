<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Designations;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $designations = Designations::all();
        return view('designations.index', compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('designations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation_name' => 'required|string|max:255|unique:designations',
            'description' => 'nullable|string',
        ]);

        Designations::create($validated);

        return redirect()->route('designations.index')
            ->with('success', 'Designation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Designations $designation)
    {
        return view('designations.edit', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designations $designation)
    {
        $validated = $request->validate([
            'designation_name' => 'required|string|max:255|unique:designations,designation_name,' . $designation->id,
            'description' => 'nullable|string',
        ]);

        $designation->update($validated);

        return redirect()->route('designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designations $designation)
    {
        try {
            $designation->delete();
            return redirect()->route('designations.index')
                ->with('success', 'Designation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('designations.index')
                ->with('error', 'Cannot delete this designation as it is being used by employees.');
        }
    }
}
