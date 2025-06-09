<?php

namespace App\Http\Controllers;

use App\Models\User; // Make sure this is at the top
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplaintsExport;
use App\Models\Road; // Assuming you have a Road model
use App\Models\Zone; // Assuming you have a Zone model
use App\Services\FirebaseUploader;

class ComplaintsController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with('zone');

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
            $user = auth()->user();

            // 🛡️ Only show complaints assigned to this technician
            if ($user->hasRole('Technical')) {
                $complaints = Complaint::where('assigned_to', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                return response()->json($complaints);
            }

            // 🧑‍💼 Admins/CC see all
            return response()->json($query->orderBy('created_at', 'desc')->get());
        }

        // Web view rendering
        $routeName = $request->route()->getName();

        if ($routeName === 'technical-complaints') {
            return view('departments.technical.complaint.index', compact('available', 'inProgress', 'query'));
        } elseif ($routeName === 'controlcenter-complaints') {
            return view('departments.controlcenter.complaint.index', compact('available', 'inProgress', 'query'));
        } else {
            return view('departments.technical.complaint.index', compact('available', 'inProgress', 'query'));
        }
    }

    public function create()
    {
        $terminals = Terminal::all();
        $branches = ['Machang', 'Kuantan', 'Kuala Terengganu']; // You may also fetch this from DB if available

        return view('departments.technical.complaint.create', compact('terminals', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'nullable|exists:terminals,id',   // ✅ Now nullable
            'zone_id'     => 'required|exists:zones,id',
            'road'        => 'required|string',
            'landmark_description' => 'nullable|string',       // ✅ New field
            'remarks'     => 'nullable|string',
            'types_of_damages' => 'nullable|array',
            'types_of_damages.*' => 'string',
            'photos.*'    => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
        ]);

        // SAFETY LOGIC: Require at least terminal_id OR landmark_description
        if (empty($request->terminal_id) && empty($request->landmark_description)) {
            return back()->withErrors('Either Terminal ID or Landmark Description is required.');
        }

        $firebase = new FirebaseUploader();

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $firebase->uploadFile($photo, 'complaints_photos');
            }
        }

        $validated['photos'] = json_encode($photoPaths);
        $validated['types_of_damages'] = $request->types_of_damages ?? [];
        $validated['status'] = 'New';
        $validated['assigned_to'] = null;

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

    // Show attend form
    public function attend($id)
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
        // ✅ Backend role protection
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('ControlCenter')) {
            abort(403, 'Unauthorized action.');
        }

        $complaint = Complaint::findOrFail($id);
        $complaint->fixed_at = now();
        $complaint->status = 'Resolved';
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint verified as Resolved.');
    }

    public function unassign($id)
    {
        // ✅ Backend role protection
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('ControlCenter')) {
            abort(403, 'Unauthorized action.');
        }

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

    public function apiIndex(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $roles = $user->getRoleNames();

        if ($roles->contains('Technical')) {
            $data = Complaint::with('zone')  // ✅ ADD THIS
                ->where('assigned_to', $user->id)
                ->whereIn('status', ['New', 'In Progress', 'Resolved'])
                ->latest()
                ->get();

            return response()->json($data, 200);
        }

        $allData = Complaint::with('zone')  // ✅ ADD THIS
            ->whereIn('status', ['New', 'In Progress', 'Resolved'])
            ->latest()
            ->get();

        return response()->json($allData->values(), 200);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'nullable|string',   // ✅ Make nullable now
            'zone' => 'required|string',
            'road' => 'required|string',
            'landmark_description' => 'nullable|string',  // ✅ Add this
            'remarks' => 'nullable|string',
            'types_of_damages' => 'nullable|array',
            'types_of_damages.*' => 'string',
            'description' => 'nullable|string',
            'status' => 'required|string',
        ]);

        if (empty($request->terminal_id) && empty($request->landmark_description)) {
            return response()->json([
                'error' => 'Either Terminal ID or Landmark Description is required.'
            ], 422);
        }

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

        // Always mark as In Progress if still New
        if ($complaint->status === 'New') {
            $complaint->status = 'In Progress';
            $complaint->attended_at = now();
        }

        // Auto-assign to current user if not already assigned
        if ($complaint->assigned_to === null) {
            $complaint->assigned_to = auth()->id();
        }

        $complaint->remarks = $request->remarks;

        // ✅ Use FirebaseUploader for fix photo
        if ($request->hasFile('fixed_photo')) {
            $firebase = new FirebaseUploader();
            $uploadedPath = $firebase->uploadFile($request->file('fixed_photo'), 'complaints_fixed_photos');
            $complaint->fixed_photo = $uploadedPath;
        }

        $complaint->save();

        return response()->json([
            'message' => 'Complaint marked as attended (In Progress)',
            'data' => $complaint
        ], 200);
    }

    public function myAttendedComplaints(Request $request)
    {
        $userId = $request->query('user_id');

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID required'
            ], 400);
        }

        $complaints = Complaint::where('assigned_to', $userId)
            ->whereIn('status', ['Resolved', 'In Progress'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($complaints);
    }

    // Show assign form
    public function assignTechnical($id)
    {
        $complaint = Complaint::findOrFail($id);
        $technicians = User::whereHas('roles', fn($q) => $q->where('name', 'Technical'))->get();

        return view('departments.technical.complaint.assign', compact('complaint', 'technicians'));
    }

    // Handle form submission
    public function updateAssignTechnical(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->assigned_to = $request->user_id;
        $complaint->save();

        return redirect()->route('technical-complaints')->with('success', 'Complaint assigned successfully.');
    }

    public function latestStatusId()
    {
        $latest = Complaint::whereIn('status', ['In Progress', 'Resolved'])
            ->latest('updated_at')
            ->first();

        return response()->json([
            'latest_id' => $latest?->id ?? 0,
            'updated_at' => $latest?->updated_at ?? now(),
            'status' => $latest?->status ?? 'N/A',
        ]);
    }
}
