<?php

namespace App\Http\Controllers;

use App\Models\Resignation;
use App\Models\Employee;
use Illuminate\Http\Request;

class FarewellController extends Controller
{
    public function create(Resignation $resignation)
    {
        $employee = $resignation->employee;
        
        return view('hr.farewell.create', [
            'resignation' => $resignation,
            'employee' => $employee
        ]);
    }

    public function store(Request $request, Resignation $resignation)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_date' => 'required|date'
        ]);

        // Add farewell email logic here
        // You can store the email in a table or send it directly

        return redirect()->route('resignations.show', $resignation)
            ->with('success', 'Farewell email has been scheduled successfully.');
    }
}
