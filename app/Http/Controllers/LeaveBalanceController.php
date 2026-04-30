<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    public function index()
    {
        $currentYear = request('year') ? request('year') : date('Y');
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', auth()->id())
            ->where('year', $currentYear)
            ->get()
            ->map(function ($balance) {
                $granted = max($balance->granted ?? 0, 0);  // Ensure granted is never negative
                $consumed = max($balance->consumed ?? 0, 0); // Ensure consumed is never negative
                return [
                    'title' => $balance->leaveType->name,
                    'granted' => $granted,
                    'consumed' => $consumed,
                    'balance' => max($balance->balance ?? 0, 0), // Ensure balance is never negative
                    'code' => $balance->leaveType->code,
                    'percentage' => $granted > 0 ? ($consumed / $granted) * 100 : 0, // Pre-calculate percentage
                ];
            });

        return view('leaves.balance', compact('leaveBalances', 'currentYear'));
    }
}
