<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CallInbound;
use App\Exports\CallInboundExport;
use Maatwebsite\Excel\Facades\Excel;

class CallInboundController extends Controller
{
    public function index(Request $request)
    {
        $query = CallInbound::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('call_time', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $calls = $query->orderBy('call_time', 'desc')->get();

        return view('departments.controlcenter.callinbound.index', compact('calls'));
    }

    public function create()
    {
        return view('departments.controlcenter.callinbound.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'caller_name'         => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'call_time'           => 'required|date',
            'category'            => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
            'department_referred' => 'nullable|string|max:100',
        ]);

        CallInbound::create($validated);
        return redirect()->route('controlcenter.callinbound.index')->with('success', 'Call record added successfully.');
    }

    public function edit(CallInbound $callinbound)
    {
        return view('departments.controlcenter.callinbound.edit', compact('callinbound'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'caller_name'         => 'required|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'call_time'           => 'required|date',
            'category'            => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
            'department_referred' => 'nullable|string|max:100',
        ]);

        $call = CallInbound::findOrFail($id);
        $call->update($validated);
        return redirect()->route('controlcenter.callinbound.index')->with('success', 'Call record updated successfully.');
    }

    public function destroy(CallInbound $callinbound)
    {
        $callinbound->delete();
        return redirect()->route('controlcenter.callinbound.index')
            ->with('success', 'Call inbound record deleted successfully.');
    }

    public function export()
    {
        return Excel::download(new CallInboundExport, 'call_inbound_records.xlsx');
    }
}
