<?php

namespace App\Http\Controllers;

use App\Models\WfhRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WfhRequestController extends Controller
{
    public function index()
    {
        $requests = WfhRequest::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
        return view('departments.hr.wfh.index', compact('requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'   => 'required|date',
            'reason' => 'required|string|min:10'
        ]);

        // Check if WFH already requested on that date
        $exists = WfhRequest::where('user_id', Auth::id())
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already applied for WFH on this date.');
        }

        WfhRequest::create([
            'user_id' => Auth::id(),
            'date'    => $request->date,
            'reason'  => $request->reason,
        ]);

        return back()->with('success', 'WFH request submitted successfully.');
    }

    // New Admin UI method to view pending WFH requests
    public function adminIndex()
    {
        // Optionally, you could filter only pending requests:
        // $requests = WfhRequest::where('status', 'Pending')->orderBy('date', 'desc')->get();
        $requests = WfhRequest::orderBy('date', 'desc')->get();
        return view('departments.hr.wfh.admin', compact('requests'));
    }

    // Admin only: Approve a WFH request
    public function approve(WfhRequest $wfhRequest)
    {
        $wfhRequest->update(['status' => 'Approved']);
        return back()->with('success', 'WFH request approved.');
    }

    // Admin only: Reject a WFH request
    public function reject(Request $request, WfhRequest $wfhRequest)
    {
        $wfhRequest->update([
            'status'         => 'Rejected',
            'approval_notes' => $request->approval_notes
        ]);
        return back()->with('success', 'WFH request rejected.');
    }
}
