<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BTS;
use Illuminate\Support\Facades\Auth;
use App\Models\Terminal;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseUploader; //added for Firebase file uploads

class BTSController extends Controller
{
    // Show BTS List
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Technical')) {
            $available = BTS::whereNull('staff_id') // 👈 Show all unassigned jobs
                ->where('action_status', 'New')
                ->latest()->get();

            $inProgress = BTS::where('staff_id', $user->staff_id) // 👈 Only their own in progress jobs
                ->where('action_status', 'In Progress')
                ->latest()->get();
        } else {
            // Admin/ControlCenter sees all
            $available = BTS::where('action_status', 'New')->latest()->get();
            $inProgress = BTS::where('action_status', 'In Progress')->latest()->get();
        }

        return view('departments.technical.bts.index', compact('available', 'inProgress'));
    }

    // Show Create BTS Form
    public function create()
    {
        return view('departments.technical.bts.create');
    }

    // Store New BTS Record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'status'          => 'required|string',
            'location'        => 'required|string',
            'event_date'      => 'required|date',
            'event_code_name' => 'required|string',
            'comment'         => 'nullable|string',
        ]);

        $validated['action_status'] = 'New'; // Always default when created
        $validated['action_by'] = null;       // No technician yet
        $validated['terminal_status'] = 'Okay'; // <-- Default it here to avoid NULL
        $validated['staff_id'] = null; // <- New BTS alert: No staff yet

        $bts = BTS::create($validated);
        //UPDATED FOR THE CSS STYLE FOR USER EXPERIENCE AND MAKE IT LIGHT UP
        return redirect()->route('bts.index')->with([
            'success' => 'BTS alert submitted successfully!',
            'highlight_id' => $bts->id,
        ]);
    }

    // Show Attend Form
    public function attend($id)
    {
        $bts = BTS::findOrFail($id);
        return view('departments.technical.bts.attend', compact('bts'));
    }

    // Update After Attending
    public function updateAttend(Request $request, $id)
    {
        $bts = BTS::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required|string',
            'parts_request' => 'required|string', // New field
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',  // <-- now required
            'terminal_status' => 'required|in:Okay,Off',
        ]);

        // Photo Upload
        $firebase = new FirebaseUploader();

        if ($request->hasFile('photo')) {
            $firebase = new FirebaseUploader();
            $validated['photo'] = $firebase->uploadFile($request->file('photo'), 'bts_photos');
        }

        $validated['staff_id'] = Auth::user()->staff_id;
        $validated['action_status'] = 'In Progress';
        $validated['action_by'] = Auth::id();
        $validated['staff_id'] = auth()->user()->staff_id ?? 'UNKNOWN';

        $bts->update($validated);

        Report::create([
            'terminal_id' => $bts->terminal_id,
            'location' => $bts->location,
            'event_date' => $bts->event_date,
            'event_code_name' => $bts->event_code_name,
            'comment' => $validated['comment'],
            'parts_request' => $validated['parts_request'],
            'photo' => $bts->photo ?? null,
            'terminal_status' => $validated['terminal_status'],
        ]);

        return redirect()->route('bts.index')->with([
            'success' => 'BTS has been Attend successfully!',
            'highlight_id' => $bts->id,
        ]);
    }

    public function searchTerminals(Request $request)
    {
        $search = $request->get('term'); // Select2 uses 'term' not 'q'

        $terminals = Terminal::where('id', 'LIKE', "%$search%")
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get(['id']);

        // return as Select2 expects: [{id: ..., text: ...}]
        $results = $terminals->map(function ($terminal) {
            return ['id' => $terminal->id, 'text' => $terminal->id];
        });

        return response()->json($results);
    }

    public function verify($id)
    {
        $bts = BTS::findOrFail($id);
        $bts->verified = true; // Assuming you have a verified column in your BTS model
        $bts->save();

        return redirect()->route('bts.index')->with('success', 'BTS attendance has been verified.');
    }

    // Show BTS Details from Control Center
    public function controlCenterView()
    {
        $available = BTS::where('action_status', 'New')->latest()->get();
        $inProgress = BTS::where('action_status', 'In Progress')->latest()->get();

        return view('departments.controlcenter.bts.index', compact('available', 'inProgress'));
    }

    public function reassign($id)
    {
        $bts = BTS::findOrFail($id);
        $bts->action_status = 'New';
        $bts->action_by = null;
        $bts->save();

        return redirect()->route('bts.index')->with('success', 'BTS alert reassigned to Available Jobs.');
    }

    public function apiIndex()
    {
        $bts = BTS::latest()->get();

        // Map the action_status to frontend-friendly values
        $mapped = $bts->map(function ($record) {
            $statusMap = [
                'New' => 'Normal',
                'In Progress' => 'Warning',
                'Failed' => 'Error' // Optional
            ];

            $record->action_status = $statusMap[$record->action_status] ?? $record->action_status;
            return $record;
        });

        return response()->json($mapped);
    }

    public function apiStore(Request $request)
    {
        try {
            Log::info('Incoming Request:', $request->all());

            $validated = $request->validate([
                'terminal_id'     => 'required|exists:terminals,id', // ✅ numeric ID directly
                'status'          => 'required|string',
                'location'        => 'required|string',
                'event_date'      => 'required|date',
                'event_code_name' => 'required|string',
                'comment'         => 'nullable|string',
            ]);

            $validated['action_status'] = 'New';
            $validated['action_by'] = null;
            $validated['terminal_status'] = 'Okay';
            $validated['staff_id'] = auth()->user()->staff_id ?? null;

            $bts = BTS::create($validated);

            return response()->json([
                'message' => 'BTS created successfully',
                'data' => $bts
            ], 201);
        } catch (\Exception $e) {
            Log::error('BTS apiStore Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        $bts = BTS::find($id);
        if (!$bts) {
            return response()->json(['error' => 'BTS record not found'], 404);
        }

        \Log::info('📥 Incoming BTS Update Request', $request->all());

        // Step 1: Extract staff_id BEFORE validation
        $incomingStaffId = $request->input('staff_id', 'UNKNOWN');

        // Step 2: Validate other fields
        $validated = $request->validate([
            'comment'         => 'nullable|string',
            'parts_request'   => 'nullable|string',
            'terminal_status' => 'nullable|in:Okay,Off',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'terminal_id'     => 'nullable|string',
            'status'          => 'nullable|string',
            'location'        => 'nullable|string',
            'event_date'      => 'nullable|date',
            'event_code_name' => 'nullable|string',
            // DO NOT validate staff_id here — keep it separate
        ]);

        $firebase = new FirebaseUploader();

        if ($request->hasFile('photo')) {
            $firebasePath = $firebase->uploadFile($request->file('photo'), 'bts_photos');
            $validated['photo'] = $firebasePath;
        }
        // Step 3: Reassign staff_id manually
        $validated['action_status'] = 'In Progress';
        $validated['action_by'] = auth()->id() ?? 1;

        $incomingStaffId = $request->request->get('staff_id') ?? $request->all()['staff_id'] ?? 'UNKNOWN';
        \Log::info('📦 Final staff_id:', ['staff_id' => $incomingStaffId]);
        \Log::info('📋 All form keys:', array_keys($request->all()));

        $validated['staff_id'] = is_string($incomingStaffId) && strlen($incomingStaffId) > 0
            ? $incomingStaffId
            : 'UNKNOWN';

        $bts->update($validated);

        return response()->json([
            'message' => 'BTS updated successfully',
            'data' => $bts->fresh()
        ]);
    }

    public function apiDelete($id)
    {
        $bts = BTS::find($id);
        if (!$bts) {
            return response()->json(['error' => 'BTS not found'], 404);
        }

        $bts->delete();

        return response()->json([
            'message' => 'BTS record deleted successfully',
            'id' => $id
        ]);
    }
}
