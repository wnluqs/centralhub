<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure this is at the top
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplaintsExport;

class ComplaintsController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::query();

        if ($request->filled('terminal_id')) {
            $query->where('terminal_id', 'like', "%{$request->terminal_id}%");
        }

        if ($request->filled('zone')) {
            $query->where('zone', 'like', "%{$request->zone}%");
        }

        $complaints = $query->get();

        // 🔁 If request is from Flutter (expects JSON)
        if ($request->wantsJson()) {
            return response()->json($complaints);
        }

        // Web view rendering
        $routeName = $request->route()->getName();

        if ($routeName === 'technical-complaints') {
            return view('departments.technical.complaint.index', compact('complaints'));
        } elseif ($routeName === 'controlcenter-complaints') {
            return view('departments.controlcenter.complaint.index', compact('complaints'));
        } else {
            return view('departments.technical.complaint.index', compact('complaints'));
        }

        $status = $request->get('status');

        $complaints = Complaint::with('technician')
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->get();
    }


    public function create()
    {
        $terminals = Terminal::all();
        $zones = ['Kuala Penyu', 'Kuala Lipis', 'Maran', 'Raub', 'Kampung Raja', 'Chukai', 'Bandar Permaisuri']; // or fetch from DB if needed
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
            'photos.*'    => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('complaint_photos', 'public');
            }
        }

        $validated['photos'] = json_encode($photoPaths);
        $validated['status'] = 'New';
        $validated['assigned_to'] = null; // Control Center will assign later

        $complaint = Complaint::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Complaint created successfully!',
                'data'    => $complaint
            ], 201);
        }

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

        $originalStatus = $complaint->status;

        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,id',
            'zone'        => 'required|string',
            'road'        => 'required|string',
            'remarks'     => 'nullable|string',
            'status'      => 'nullable|string',
            'types_of_damages' => 'nullable|string',
        ]);

        // Update base data
        $complaint->fill($validated);

        // Handle fixed_at auto-time
        if ($originalStatus === 'In Progress' && $request->status === 'Resolved' && !$complaint->fixed_at) {
            $complaint->fixed_at = now(); // ✅ Automatically log end time
        }

        $complaint->save();

        return redirect()->route('complaints.index')->with('success', 'Complaint updated successfully.');
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

    // Show assign form
    public function assign($id)
    {
        $complaint = Complaint::findOrFail($id);
        $technicians = User::role('Technical')->get(); // Spatie role-based
        return view('departments.technical.complaint.assign', compact('complaint', 'technicians'));
    }

    // Handle assignment
    public function assignUpdate(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
            'types_of_damages' => 'required|array',
            'types_of_damages.*' => 'string',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = $request->technician_id;
        $complaint->types_of_damages = json_encode($request->types_of_damages);
        $complaint->status = 'In Progress';
        $complaint->attended_at = now();
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint assigned with multiple damages.');
    }

    // Show reassign form (same as assign)
    public function reassign($id)
    {
        $complaint = Complaint::findOrFail($id);
        $technicians = User::role('Technical')->get(); // Spatie-based role
        return view('departments.technical.complaint.reassign', compact('complaint', 'technicians'));
    }

    // Handle reassignment
    public function reassignUpdate(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = $request->technician_id;
        $complaint->status = 'In Progress'; // Or keep as-is depending on your logic
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint reassigned successfully.');
    }

    public function markAsFixed($id)
    {
        $complaint = Complaint::findOrFail($id);

        // Only allow if currently 'In Progress'
        if ($complaint->status === 'In Progress') {
            $complaint->fixed_at = now(); // Set current time
            $complaint->status = 'Resolved'; // Updated status
            $complaint->save();
        }

        return redirect()->back()->with('success', 'Complaint marked as fixed.');
    }
}
