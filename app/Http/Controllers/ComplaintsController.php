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

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $available = $query->where('status', 'New')->orderByDesc('created_at')->get();
        $inProgress = Complaint::whereIn('status', ['In Progress', 'Resolved'])->orderByDesc('created_at')->get();
        $complaints = $query->orderBy('created_at', 'desc')->get(); // Latest on top

        // 🔁 If request is from Flutter (expects JSON)
        if ($request->wantsJson()) {
            return response()->json($complaints);
        }

        // Web view rendering
        $routeName = $request->route()->getName();

        if ($routeName === 'technical-complaints') {
            return view('departments.technical.complaint.index', compact('available', 'inProgress'));
        } elseif ($routeName === 'controlcenter-complaints') {
            return view('departments.controlcenter.complaint.index', compact('available', 'inProgress'));
        } else {
            return view('departments.technical.complaint.index', compact('available', 'inProgress'));
        }
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
            'types_of_damages' => 'nullable|array',
            'types_of_damages.*' => 'string',
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
        $validated['types_of_damages'] = json_encode($request->types_of_damages ?? []);
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
        return view('departments.technical.complaint.attend', compact('complaint', 'technicians'));
    }

    // Handle assignment
    public function assignUpdate(Request $request, $id)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = $request->technician_id;
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
        $complaint->status = 'New'; // instead of 'In Progress'
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint reassigned successfully.');
    }

    public function markFixed($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->fixed_at = now();
        $complaint->status = 'Resolved';
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint verified as Resolved.');
    }

    public function unassign($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = null;
        $complaint->attended_at = null;
        $complaint->status = 'New';
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint moved back to Available Jobs.');
    }

    public function submitAttendance(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        // Set who attended
        $complaint->assigned_to = auth()->id(); // or your specific technician_id logic
        $complaint->attended_at = now();
        $complaint->status = 'In Progress';
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Attendance submitted successfully.');
    }

    public function apiIndex()
    {
        $complaints = Complaint::latest()->get();
        return response()->json($complaints);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|string', // ← you should ensure this is here!
            'zone' => 'required|string',
            'road' => 'required|string',
            'remarks' => 'nullable|string',
            'types_of_damages' => 'nullable|array',
            'types_of_damages.*' => 'string',
            'description' => 'nullable|string',
            'status' => 'required|string', // e.g., Pending, Resolved
            // add more fields as needed
        ]);

        // ✅ Encode the array if it exists
        if (isset($validated['types_of_damages'])) {
            $validated['types_of_damages'] = json_encode($validated['types_of_damages']);
        }

        $complaint = Complaint::create($validated);

        return response()->json([
            'message' => 'Complaint created successfully',
            'data' => $complaint
        ], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found'], 404);
        }

        $validated = $request->validate([
            'zone'        => 'sometimes|string',
            'road'        => 'sometimes|string',
            'remarks'     => 'nullable|string',
            'terminal_id' => 'sometimes|string',
            'title'       => 'sometimes|string',
            'description' => 'nullable|string',
            'status'      => 'sometimes|string',
        ]);

        $complaint->update($validated);

        return response()->json([
            'message' => 'Complaint updated successfully',
            'data' => $complaint->fresh()
        ]);
    }

    public function apiDelete($id)
    {
        $complaint = Complaint::find($id);
        if (!$complaint) {
            return response()->json(['error' => 'Complaint not found'], 404);
        }

        $complaint->delete();

        return response()->json([
            'message' => 'Complaint deleted successfully',
            'id' => $id
        ]);
    }

    public function apiResolve(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $complaint->remarks = $request->remarks;
        $complaint->status = 'Resolved';
        $complaint->fixed_at = now();

        // Handle uploaded photo if available
        if ($request->hasFile('fixed_photo')) {
            $file = $request->file('fixed_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/complaint_photos', $filename);
            $complaint->fixed_photo = $filename; // optional column
        }

        $complaint->save();

        return response()->json(['message' => 'Complaint resolved successfully!', 'data' => $complaint], 200);
    }
}
