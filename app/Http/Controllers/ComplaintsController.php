<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplaintsExport;

class ComplaintsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Start a query
        $query = Complaint::query();

        // 2. Filter by terminal_id if provided
        if ($request->filled('terminal_id')) {
            // Use 'like' for partial matching
            $query->where('terminal_id', 'like', "%{$request->terminal_id}%");
        }

        // 3. Filter by zone if provided
        if ($request->filled('zone')) {
            // Use 'like' for partial matching
            $query->where('zone', 'like', "%{$request->zone}%");
        }

        // 4. Retrieve the filtered results
        $complaints = $query->get();

        // Check the route name to decide which view to load
        $routeName = $request->route()->getName();

        if ($routeName === 'technical-complaints') {
            return view('departments.technical.complaint.index', compact('complaints'));
        } else if ($routeName === 'controlcenter-complaints') {
            return view('departments.controlcenter.complaint.index', compact('complaints'));
        } else {
            // Fallback or default view
            return view('departments.technical.complaint.index', compact('complaints'));
        }
    }

    public function create()
    {
        $terminals = Terminal::all();
        $zones = ['Kuala Penyu', 'Kuala Lipis', 'Maran', 'Raub']; // or fetch from DB if needed
        $roads = ['Jalan Himalaya', 'Jalan Ampang', 'Jalan Bukit Tinggi', 'Jalan Starlight']; // or fetch from DB if needed

        return view('departments.technical.complaint.create', compact('zones', 'terminals', 'roads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,id',
            'zone'        => 'required|string',
            'road'        => 'required|string',
            'remarks'     => 'nullable|string',
            'photos'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photos')) {
            $validated['photos'] = $request->file('photos')->store('complaint_photos', 'public');
        }

        Complaint::create($validated);
        return redirect()->route('complaints.index')->with('success', 'Complaint created successfully!');
    }

    public function edit($id)
    {
        $complaint = Complaint::findOrFail($id);
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        return view('complaints.edit', compact('complaint', 'statusOptions'));
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,id',
            'zone'        => 'required|string',
            'road'        => 'required|string',
            'remarks'     => 'nullable|string',
            'photos'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($request->hasFile('photos')) {
            $validated['photos'] = $request->file('photos')->store('complaint_photos', 'public');
        }
        $complaint->update($validated);
        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully!');
    }

    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->delete();
        return redirect()->route('complaints.index')->with('success', 'Complaint deleted successfully!');
    }

    public function exportExcel(Request $request)
    {
        // Currently, you might just do:
        // return Excel::download(new ComplaintsExport, 'complaints.xlsx');

        // Instead, pass the filter values to the ComplaintsExport constructor:
        return \Maatwebsite\Excel\Facades\Excel::download(
            new ComplaintsExport($request->terminal_id, $request->zone),
            'complaints.xlsx'
        );
    }

    public function exportCSV(Request $request)
    {
        // Similarly for CSV
        return \Maatwebsite\Excel\Facades\Excel::download(
            new ComplaintsExport($request->terminal_id, $request->zone),
            'complaints.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}
